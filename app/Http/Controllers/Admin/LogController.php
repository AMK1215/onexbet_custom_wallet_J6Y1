<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTransaction;
use App\Models\TransactionLog;
use App\Models\User;
use App\Models\Admin\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    /**
     * Display the main logs dashboard
     */
    public function index(Request $request)
    {
        $logTypes = [
            'custom_transactions' => 'Custom Wallet Transactions',
            'webhook_logs' => 'Webhook API Logs',
            'system_logs' => 'System Logs',
            'user_activities' => 'User Activities'
        ];

        $selectedType = $request->get('type', 'custom_transactions');
        
        return view('admin.logs.index', compact('logTypes', 'selectedType'));
    }

    /**
     * Display custom wallet transactions
     */
    public function customTransactions(Request $request)
    {
        $query = CustomTransaction::with(['user', 'targetUser'])
            ->active() // Only show active (non-deleted) transactions
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('transaction_name')) {
            $query->where('transaction_name', $request->transaction_name);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $transactions = $query->paginate(50);

        // Get filter options
        $users = User::whereIn('type', ['Player', 'Agent', 'Master'])->get();
        $transactionTypes = CustomTransaction::active()->distinct()->pluck('type');
        $transactionNames = CustomTransaction::active()->distinct()->pluck('transaction_name');

        return view('admin.logs.custom_transactions', compact(
            'transactions', 
            'users', 
            'transactionTypes', 
            'transactionNames'
        ));
    }

    /**
     * Display webhook API logs
     */
    public function webhookLogs(Request $request)
    {
        $query = TransactionLog::orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Get filter options
        $logTypes = TransactionLog::distinct()->pluck('type');
        $statuses = TransactionLog::distinct()->pluck('status');

        return view('admin.logs.webhook_logs', compact('logs', 'logTypes', 'statuses'));
    }

    /**
     * Display system logs from Laravel log files
     */
    public function systemLogs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        $logStats = [
            'total' => 0,
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0
        ];

        if (file_exists($logFile)) {
            // Read log file efficiently
            $logContent = file_get_contents($logFile);
            $logLines = explode("\n", $logContent);
            
            // Filter logs based on request parameters
            $filteredLines = [];
            $level = $request->get('level', 'all');
            $search = $request->get('search', '');
            
            foreach ($logLines as $line) {
                if (empty(trim($line))) continue;
                
                // Count log levels for statistics
                $logStats['total']++;
                if (str_contains(strtoupper($line), 'ERROR')) $logStats['error']++;
                elseif (str_contains(strtoupper($line), 'WARNING')) $logStats['warning']++;
                elseif (str_contains(strtoupper($line), 'INFO')) $logStats['info']++;
                elseif (str_contains(strtoupper($line), 'DEBUG')) $logStats['debug']++;
                
                // Filter by level
                if ($level !== 'all' && !str_contains(strtoupper($line), strtoupper($level))) {
                    continue;
                }
                
                // Filter by search term
                if ($search && !str_contains(strtolower($line), strtolower($search))) {
                    continue;
                }
                
                $filteredLines[] = [
                    'content' => $line,
                    'level' => $this->extractLogLevel($line),
                    'timestamp' => $this->extractTimestamp($line)
                ];
            }
            
            // Reverse to show newest first and limit results
            $logs = array_reverse(array_slice($filteredLines, -1000));
        }

        return view('admin.logs.system_logs', compact('logs', 'logStats'));
    }

    /**
     * Extract log level from log line
     */
    private function extractLogLevel($line)
    {
        if (str_contains(strtoupper($line), 'ERROR')) return 'ERROR';
        if (str_contains(strtoupper($line), 'WARNING')) return 'WARNING';
        if (str_contains(strtoupper($line), 'INFO')) return 'INFO';
        if (str_contains(strtoupper($line), 'DEBUG')) return 'DEBUG';
        return 'UNKNOWN';
    }

    /**
     * Extract timestamp from log line
     */
    private function extractTimestamp($line)
    {
        // Try to extract timestamp from Laravel log format
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Display user activity logs from user_logs table
     */
    public function userActivities(Request $request)
    {
        $query = UserLog::with(['user' => function($q) {
            $q->select('id', 'user_name', 'name', 'email', 'type', 'balance');
        }])->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('user_type')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('type', $request->user_type);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', "%{$request->ip_address}%");
        }

        if ($request->filled('func_access')) {
            $query->where('func_access', 'like', "%{$request->func_access}%");
        }

        $userLogs = $query->paginate(50);

        // Get filter options
        $userTypes = User::distinct()->pluck('type');
        $ipAddresses = UserLog::distinct()->pluck('ip_address');
        $funcAccess = UserLog::distinct()->pluck('func_access');

        return view('admin.logs.user_activities', compact('userLogs', 'userTypes', 'ipAddresses', 'funcAccess'));
    }

    /**
     * Show detailed transaction log
     */
    public function showTransaction($id)
    {
        $transaction = CustomTransaction::with(['user', 'targetUser'])->findOrFail($id);
        
        return view('admin.logs.transaction_detail', compact('transaction'));
    }

    /**
     * Show detailed webhook log
     */
    public function showWebhookLog($id)
    {
        $log = TransactionLog::findOrFail($id);
        
        return view('admin.logs.webhook_detail', compact('log'));
    }

    /**
     * Export transactions to CSV
     */
    public function exportTransactions(Request $request)
    {
        $query = CustomTransaction::with(['user', 'targetUser']);

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'custom_transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'User', 'Target User', 'Amount', 'Type', 'Transaction Name',
                'Old Balance', 'New Balance', 'Meta', 'UUID', 'Created At'
            ]);

            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->user->user_name ?? 'N/A',
                    $transaction->targetUser->user_name ?? 'N/A',
                    $transaction->amount,
                    $transaction->type,
                    $transaction->transaction_name,
                    $transaction->old_balance,
                    $transaction->new_balance,
                    json_encode($transaction->meta),
                    $transaction->uuid,
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get real-time log statistics
     */
    public function getStats()
    {
        $stats = [
            'total_transactions' => CustomTransaction::count(),
            'today_transactions' => CustomTransaction::whereDate('created_at', today())->count(),
            'total_deposits' => CustomTransaction::where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => CustomTransaction::where('type', 'withdraw')->sum('amount'),
            'webhook_logs_today' => TransactionLog::whereDate('created_at', today())->count(),
            'failed_webhooks' => TransactionLog::where('status', '!=', 'success')->count(),
            'user_activities_today' => UserLog::whereDate('created_at', today())->count(),
            'total_user_logs' => UserLog::count(),
            'unique_users_today' => UserLog::whereDate('created_at', today())->distinct('user_id')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Clear old logs (admin only)
     */
    public function clearOldLogs(Request $request)
    {
        $days = $request->get('days', 30);
        $cutoffDate = now()->subDays($days);

        // Clear old custom transactions
        $deletedTransactions = CustomTransaction::where('created_at', '<', $cutoffDate)->delete();

        // Clear old webhook logs
        $deletedWebhookLogs = TransactionLog::where('created_at', '<', $cutoffDate)->delete();

        // Clear old user logs
        $deletedUserLogs = UserLog::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'message' => "Cleared {$deletedTransactions} transactions, {$deletedWebhookLogs} webhook logs, and {$deletedUserLogs} user logs older than {$days} days",
            'deleted_transactions' => $deletedTransactions,
            'deleted_webhook_logs' => $deletedWebhookLogs,
            'deleted_user_logs' => $deletedUserLogs
        ]);
    }

    /**
     * Soft delete multiple transactions with balance verification
     */
    public function softDeleteTransactions(Request $request)
    {
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'integer|exists:custom_transactions,id',
            'reason' => 'required|string|max:500'
        ]);

        $transactionIds = $request->transaction_ids;
        $reason = $request->reason;
        $deletedBy = auth()->user();

        // Get transactions with balance verification
        $transactions = CustomTransaction::active()
            ->with(['user', 'targetUser'])
            ->whereIn('id', $transactionIds)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid transactions found for deletion'
            ], 400);
        }

        // Balance verification warning
        $balanceWarnings = [];
        foreach ($transactions as $transaction) {
            if ($transaction->type === 'deposit' || $transaction->type === 'withdraw') {
                $currentBalance = $transaction->user->balance;
                $expectedBalance = $transaction->new_balance;
                
                if (abs($currentBalance - $expectedBalance) > 0.01) {
                    $balanceWarnings[] = [
                        'transaction_id' => $transaction->id,
                        'user' => $transaction->user->user_name,
                        'current_balance' => $currentBalance,
                        'expected_balance' => $expectedBalance,
                        'difference' => $currentBalance - $expectedBalance
                    ];
                }
            }
        }

        // Soft delete transactions
        $deletedCount = 0;
        foreach ($transactions as $transaction) {
            $transaction->softDelete($deletedBy, $reason);
            $deletedCount++;
        }

        // Log the deletion action
        Log::warning('CustomTransaction soft delete', [
            'deleted_by' => $deletedBy->id,
            'deleted_by_name' => $deletedBy->user_name,
            'transaction_ids' => $transactionIds,
            'reason' => $reason,
            'deleted_count' => $deletedCount,
            'balance_warnings' => $balanceWarnings
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully soft deleted {$deletedCount} transactions",
            'deleted_count' => $deletedCount,
            'balance_warnings' => $balanceWarnings
        ]);
    }

    /**
     * Restore soft deleted transactions
     */
    public function restoreTransactions(Request $request)
    {
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'integer|exists:custom_transactions,id'
        ]);

        $transactionIds = $request->transaction_ids;
        $restoredBy = auth()->user();

        // Get soft deleted transactions
        $transactions = CustomTransaction::onlyTrashed()
            ->with(['user', 'targetUser'])
            ->whereIn('id', $transactionIds)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No deleted transactions found for restoration'
            ], 400);
        }

        // Restore transactions
        $restoredCount = 0;
        foreach ($transactions as $transaction) {
            $transaction->restore();
            $restoredCount++;
        }

        // Log the restoration action
        Log::info('CustomTransaction restore', [
            'restored_by' => $restoredBy->id,
            'restored_by_name' => $restoredBy->user_name,
            'transaction_ids' => $transactionIds,
            'restored_count' => $restoredCount
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully restored {$restoredCount} transactions",
            'restored_count' => $restoredCount
        ]);
    }

    /**
     * View soft deleted transactions
     */
    public function deletedTransactions(Request $request)
    {
        $query = CustomTransaction::onlyTrashed()
            ->with(['user', 'targetUser', 'deletedBy'])
            ->orderBy('deleted_at', 'desc');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('deleted_by')) {
            $query->where('deleted_by', $request->deleted_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(50);

        // Get filter options
        $users = User::whereIn('type', ['Player', 'Agent', 'Master'])->get();
        $transactionTypes = CustomTransaction::onlyTrashed()->distinct()->pluck('type');
        $admins = User::whereIn('type', ['Owner', 'Master'])->get();

        return view('admin.logs.deleted_transactions', compact(
            'transactions', 
            'users', 
            'transactionTypes', 
            'admins'
        ));
    }
}
