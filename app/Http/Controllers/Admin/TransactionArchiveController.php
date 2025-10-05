<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TransactionArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionArchiveController extends Controller
{
    protected $archiveService;

    public function __construct(TransactionArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
        $this->middleware(function ($request, $next) {
            $userType = auth()->user()->type;
            if (!in_array($userType, [10, 50])) { // Owner = 10, SystemWallet = 50
                abort(403, 'Access denied. Only Owner and SystemWallet roles can access transaction archive.');
            }
            return $next($request);
        });
    }

    /**
     * Display the transaction archive dashboard
     */
    public function index()
    {
        $stats = $this->archiveService->getArchiveStats();
        
        // Get recent archive batches
        $recentBatches = \DB::table('archived_custom_transactions')
            ->select('archive_batch_id', 'archived_at')
            ->whereNotNull('archive_batch_id')
            ->groupBy('archive_batch_id', 'archived_at')
            ->orderBy('archived_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.transaction-archive.index', compact('stats', 'recentBatches'));
    }

    /**
     * Show archive statistics and details
     */
    public function stats()
    {
        $stats = $this->archiveService->getArchiveStats();
        
        // Get detailed statistics
        $detailedStats = [
            'main_table' => [
                'total_transactions' => \App\Models\CustomTransaction::count(),
                'deposits' => \App\Models\CustomTransaction::where('type', 'deposit')->count(),
                'withdrawals' => \App\Models\CustomTransaction::where('type', 'withdraw')->count(),
                'transfers' => \App\Models\CustomTransaction::where('type', 'transfer')->count(),
                'oldest_transaction' => \App\Models\CustomTransaction::min('created_at'),
                'newest_transaction' => \App\Models\CustomTransaction::max('created_at'),
            ],
            'archive_table' => [
                'total_transactions' => \DB::table('archived_custom_transactions')->count(),
                'deposits' => \DB::table('archived_custom_transactions')->where('type', 'deposit')->count(),
                'withdrawals' => \DB::table('archived_custom_transactions')->where('type', 'withdraw')->count(),
                'transfers' => \DB::table('archived_custom_transactions')->where('type', 'transfer')->count(),
                'oldest_transaction' => \DB::table('archived_custom_transactions')->min('created_at'),
                'newest_transaction' => \DB::table('archived_custom_transactions')->max('created_at'),
            ]
        ];

        return response()->json($detailedStats);
    }

    /**
     * Perform manual archive operation
     */
    public function archive(Request $request)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:60',
            'reason' => 'nullable|string|max:500'
        ]);

        $months = $request->months;
        $reason = $request->reason ?? 'Manual archive by admin';

        try {
            Log::info('Manual transaction archive started', [
                'months' => $months,
                'reason' => $reason,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->user_name
            ]);

            $results = $this->archiveService->archiveOldTransactions($months);

            if ($results['success']) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully archived {$results['archived_count']} transactions",
                    'data' => $results
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Archive failed: ' . $results['error'],
                    'data' => $results
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Manual transaction archive failed', [
                'months' => $months,
                'reason' => $reason,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Archive operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform dry run to see what would be archived
     */
    public function dryRun(Request $request)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:60'
        ]);

        $months = $request->months;
        $cutoffDate = now()->subMonths($months);
        
        $countToArchive = \App\Models\CustomTransaction::where('created_at', '<', $cutoffDate)->count();
        
        $sampleTransactions = \App\Models\CustomTransaction::where('created_at', '<', $cutoffDate)
            ->with(['user', 'targetUser'])
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'months' => $months,
                'cutoff_date' => $cutoffDate,
                'count_to_archive' => $countToArchive,
                'sample_transactions' => $sampleTransactions
            ]
        ]);
    }

    /**
     * Optimize the main transactions table
     */
    public function optimize()
    {
        try {
            $results = $this->archiveService->optimizeMainTable();

            if ($results['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Table optimization completed successfully',
                    'data' => $results
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Table optimization failed: ' . $results['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Table optimization failed', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Optimization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View archived transactions
     */
    public function viewArchived(Request $request)
    {
        $query = \DB::table('archived_custom_transactions')
            ->select([
                'id',
                'original_id',
                'user_id',
                'target_user_id',
                'amount',
                'type',
                'transaction_name',
                'old_balance',
                'new_balance',
                'created_at',
                'archived_at',
                'archive_batch_id'
            ])
            ->orderBy('archived_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('batch_id')) {
            $query->where('archive_batch_id', $request->batch_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('archived_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('archived_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(50);

        // Get filter options
        $transactionTypes = \DB::table('archived_custom_transactions')
            ->distinct()
            ->pluck('type');

        $batchIds = \DB::table('archived_custom_transactions')
            ->distinct()
            ->whereNotNull('archive_batch_id')
            ->pluck('archive_batch_id');

        return view('admin.transaction-archive.view-archived', compact(
            'transactions',
            'transactionTypes',
            'batchIds'
        ));
    }

    /**
     * Restore archived transactions (emergency use only)
     */
    public function restore(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|string',
            'reason' => 'required|string|max:500'
        ]);

        $batchId = $request->batch_id;
        $reason = $request->reason;

        try {
            Log::warning('Transaction restore initiated', [
                'batch_id' => $batchId,
                'reason' => $reason,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->user_name
            ]);

            $results = $this->archiveService->restoreArchivedTransactions($batchId);

            if ($results['success']) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully restored {$results['restored_count']} transactions",
                    'data' => $results
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Restore failed: ' . $results['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Transaction restore failed', [
                'batch_id' => $batchId,
                'reason' => $reason,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Restore operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archive batch details
     */
    public function batchDetails($batchId)
    {
        $batchTransactions = \DB::table('archived_custom_transactions')
            ->where('archive_batch_id', $batchId)
            ->orderBy('created_at', 'asc')
            ->get();

        $batchInfo = [
            'batch_id' => $batchId,
            'total_transactions' => $batchTransactions->count(),
            'archived_at' => $batchTransactions->first()->archived_at ?? null,
            'oldest_transaction' => $batchTransactions->min('created_at'),
            'newest_transaction' => $batchTransactions->max('created_at'),
            'total_amount' => $batchTransactions->sum('amount'),
            'types' => $batchTransactions->groupBy('type')->map->count()
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'batch_info' => $batchInfo,
                'transactions' => $batchTransactions
            ]
        ]);
    }
}
