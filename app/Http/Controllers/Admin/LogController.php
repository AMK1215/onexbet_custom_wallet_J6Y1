<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTransaction;
use App\Models\TransactionLog;
use App\Models\User;
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
        $transactionTypes = CustomTransaction::distinct()->pluck('type');
        $transactionNames = CustomTransaction::distinct()->pluck('transaction_name');

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

        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $logLines = explode("\n", $logContent);
            
            // Filter logs based on request parameters
            $filteredLines = [];
            $level = $request->get('level', 'all');
            $search = $request->get('search', '');
            
            foreach ($logLines as $line) {
                if (empty(trim($line))) continue;
                
                // Filter by level
                if ($level !== 'all' && !str_contains($line, strtoupper($level))) {
                    continue;
                }
                
                // Filter by search term
                if ($search && !str_contains(strtolower($line), strtolower($search))) {
                    continue;
                }
                
                $filteredLines[] = $line;
            }
            
            // Reverse to show newest first and limit results
            $logs = array_reverse(array_slice($filteredLines, -1000));
        }

        return view('admin.logs.system_logs', compact('logs'));
    }

    /**
     * Display user activity logs
     */
    public function userActivities(Request $request)
    {
        $query = User::with(['customTransactions' => function($q) {
            $q->orderBy('created_at', 'desc')->limit(10);
        }])->whereIn('type', ['Player', 'Agent', 'Master']);

        // Apply filters
        if ($request->filled('user_type')) {
            $query->where('type', $request->user_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20);

        return view('admin.logs.user_activities', compact('users'));
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

        return response()->json([
            'message' => "Cleared {$deletedTransactions} transactions and {$deletedWebhookLogs} webhook logs older than {$days} days",
            'deleted_transactions' => $deletedTransactions,
            'deleted_webhook_logs' => $deletedWebhookLogs
        ]);
    }
}
