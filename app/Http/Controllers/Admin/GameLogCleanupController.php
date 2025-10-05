<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameLogCleanupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameLogCleanupController extends Controller
{
    protected $cleanupService;

    public function __construct(GameLogCleanupService $cleanupService)
    {
        $this->cleanupService = $cleanupService;
        $this->middleware(function ($request, $next) {
            $userType = auth()->user()->type;
            if (!in_array($userType, [10, 50])) { // Owner = 10, SystemWallet = 50
                abort(403, 'Access denied. Only Owner and SystemWallet roles can access game log cleanup.');
            }
            return $next($request);
        });
    }

    /**
     * Display the game log cleanup dashboard
     */
    public function index()
    {
        $stats = $this->cleanupService->getCleanupStats();
        $recentCleanups = $this->cleanupService->getRecentCleanups();
        
        return view('admin.game-log-cleanup.index', compact('stats', 'recentCleanups'));
    }

    /**
     * Show cleanup statistics
     */
    public function stats()
    {
        $stats = $this->cleanupService->getCleanupStats();
        return response()->json($stats);
    }

    /**
     * Perform manual cleanup operation
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
            'reason' => 'nullable|string|max:500'
        ]);

        $days = $request->days;
        $reason = $request->reason ?? 'Manual cleanup by admin';

        try {
            Log::info('Manual game log cleanup started', [
                'days' => $days,
                'reason' => $reason,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->user_name
            ]);

            $results = $this->cleanupService->deleteOldGameLogs($days);

            if ($results['success']) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully deleted {$results['deleted_count']} game logs",
                    'data' => $results
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cleanup failed: ' . $results['error'],
                    'data' => $results
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Manual game log cleanup failed', [
                'days' => $days,
                'reason' => $reason,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cleanup operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform dry run to see what would be deleted
     */
    public function preview(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->days;
        $results = $this->cleanupService->previewCleanup($days);

        if ($results['success']) {
            return response()->json([
                'success' => true,
                'data' => $results['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $results['error']
            ], 500);
        }
    }

    /**
     * Optimize the place_bets table
     */
    public function optimize()
    {
        try {
            $results = $this->cleanupService->optimizeTable();

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
     * Get recent cleanup operations
     */
    public function recentCleanups()
    {
        $cleanups = $this->cleanupService->getRecentCleanups();
        return response()->json([
            'success' => true,
            'data' => $cleanups
        ]);
    }
}
