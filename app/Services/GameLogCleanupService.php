<?php

namespace App\Services;

use App\Models\PlaceBet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GameLogCleanupService
{
    /**
     * Delete game logs older than specified days
     */
    public function deleteOldGameLogs(int $daysOld = 15): array
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        $results = [
            'deleted_count' => 0,
            'errors' => [],
            'start_time' => now(),
            'cutoff_date' => $cutoffDate,
            'days_old' => $daysOld
        ];

        try {
            DB::transaction(function () use ($cutoffDate, &$results) {
                // Count records to be deleted
                $countToDelete = PlaceBet::where('created_at', '<', $cutoffDate)->count();
                $results['total_to_delete'] = $countToDelete;

                if ($countToDelete === 0) {
                    Log::info('No game logs found to delete');
                    return;
                }

                // Delete in chunks to avoid memory issues
                $deletedCount = 0;
                $chunkSize = 1000;
                
                PlaceBet::where('created_at', '<', $cutoffDate)
                    ->chunkById($chunkSize, function ($bets) use (&$deletedCount) {
                        foreach ($bets as $bet) {
                            try {
                                $bet->delete();
                                $deletedCount++;
                            } catch (\Exception $e) {
                                $results['errors'][] = [
                                    'bet_id' => $bet->id,
                                    'error' => $e->getMessage()
                                ];
                            }
                        }
                    });

                $results['deleted_count'] = $deletedCount;

                // Log the operation
                Log::info('Game log cleanup completed', [
                    'deleted_count' => $deletedCount,
                    'cutoff_date' => $cutoffDate,
                    'days_old' => $results['days_old'],
                    'errors_count' => count($results['errors'])
                ]);
            });

            $results['success'] = true;
            $results['duration'] = now()->diffInSeconds($results['start_time']);

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            Log::error('Game log cleanup failed', [
                'error' => $e->getMessage(),
                'cutoff_date' => $cutoffDate,
                'days_old' => $results['days_old']
            ]);
        }

        return $results;
    }

    /**
     * Get cleanup statistics
     */
    public function getCleanupStats(): array
    {
        try {
            $stats = [
                'total_bets' => PlaceBet::count(),
                'bets_older_than_15_days' => PlaceBet::where('created_at', '<', Carbon::now()->subDays(15))->count(),
                'bets_older_than_30_days' => PlaceBet::where('created_at', '<', Carbon::now()->subDays(30))->count(),
                'oldest_bet' => PlaceBet::min('created_at'),
                'newest_bet' => PlaceBet::max('created_at'),
                'table_size' => $this->getTableSize('place_bets')
            ];

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get cleanup stats', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Preview what would be deleted (dry run)
     */
    public function previewCleanup(int $daysOld = 15): array
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        
        try {
            $countToDelete = PlaceBet::where('created_at', '<', $cutoffDate)->count();
            
            $sampleBets = PlaceBet::where('created_at', '<', $cutoffDate)
                ->with(['user'])
                ->limit(10)
                ->get();

            return [
                'success' => true,
                'data' => [
                    'days_old' => $daysOld,
                    'cutoff_date' => $cutoffDate,
                    'count_to_delete' => $countToDelete,
                    'sample_bets' => $sampleBets
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Optimize the place_bets table
     */
    public function optimizeTable(): array
    {
        $results = [
            'start_time' => now(),
            'operations' => []
        ];

        try {
            $driver = DB::getDriverName();
            
            if ($driver === 'pgsql') {
                // PostgreSQL optimization
                DB::statement('ANALYZE place_bets');
                $results['operations'][] = 'Table analyzed';

                DB::statement('REINDEX TABLE place_bets');
                $results['operations'][] = 'Table reindexed';

                DB::statement('VACUUM place_bets');
                $results['operations'][] = 'Table vacuumed';

            } else {
                // MySQL optimization
                DB::statement('ANALYZE TABLE place_bets');
                $results['operations'][] = 'Table analyzed';

                DB::statement('OPTIMIZE TABLE place_bets');
                $results['operations'][] = 'Table optimized';

                $checkResult = DB::select('CHECK TABLE place_bets');
                if ($checkResult[0]->Msg_text !== 'OK') {
                    DB::statement('REPAIR TABLE place_bets');
                    $results['operations'][] = 'Table repaired';
                }
            }

            $results['success'] = true;
            $results['duration'] = now()->diffInSeconds($results['start_time']);

            Log::info('Place bets table optimized', $results);

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            Log::error('Table optimization failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Get table size in MB
     */
    private function getTableSize(string $tableName): float
    {
        try {
            $driver = DB::getDriverName();
            
            if ($driver === 'pgsql') {
                // PostgreSQL syntax
                $result = DB::select("
                    SELECT 
                        ROUND(pg_total_relation_size(?) / 1024.0 / 1024.0, 2) AS size_mb
                ", [$tableName]);
                
                return $result[0]->size_mb ?? 0;
            } else {
                // MySQL syntax
                $result = DB::select("
                    SELECT 
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                    FROM information_schema.TABLES 
                    WHERE table_schema = DATABASE() 
                    AND table_name = ?
                ", [$tableName]);

                return $result[0]->size_mb ?? 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get recent cleanup operations
     */
    public function getRecentCleanups(): array
    {
        try {
            // Get recent logs from Laravel log
            $logFile = storage_path('logs/laravel.log');
            $cleanups = [];
            
            if (file_exists($logFile)) {
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $recentLines = array_slice($lines, -1000); // Last 1000 lines
                
                foreach ($recentLines as $line) {
                    if (strpos($line, 'Game log cleanup completed') !== false) {
                        // Extract timestamp and details
                        preg_match('/\[(.*?)\].*Game log cleanup completed.*deleted_count.*?(\d+).*?days_old.*?(\d+)/', $line, $matches);
                        if (count($matches) >= 4) {
                            $cleanups[] = [
                                'timestamp' => $matches[1],
                                'deleted_count' => $matches[2],
                                'days_old' => $matches[3]
                            ];
                        }
                    }
                }
                
                // Sort by timestamp descending
                usort($cleanups, function($a, $b) {
                    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                });
                
                return array_slice($cleanups, 0, 10); // Return last 10 cleanups
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get recent cleanups', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
