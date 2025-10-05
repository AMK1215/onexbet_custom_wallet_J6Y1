<?php

namespace App\Console\Commands;

use App\Services\GameLogCleanupService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupOldGameLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game-logs:cleanup 
                            {--days=15 : Number of days old game logs to delete}
                            {--dry-run : Show what would be deleted without actually doing it}
                            {--optimize : Optimize the table after cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old game logs from place_bets table (older than specified days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $optimize = $this->option('optimize');

        $this->info("🔄 Starting game log cleanup process...");
        $this->info("📅 Deleting game logs older than {$days} days");

        if ($dryRun) {
            $this->warn("🧪 DRY RUN MODE - No actual changes will be made");
        }

        // Show current stats
        $cleanupService = new GameLogCleanupService();
        $stats = $cleanupService->getCleanupStats();

        $this->table(['Metric', 'Count'], [
            ['Total Game Logs', number_format($stats['total_bets'] ?? 0)],
            ['Logs Older Than 15 Days', number_format($stats['bets_older_than_15_days'] ?? 0)],
            ['Logs Older Than 30 Days', number_format($stats['bets_older_than_30_days'] ?? 0)],
            ['Table Size (MB)', $stats['table_size'] ?? 0],
            ['Oldest Log', $stats['oldest_bet'] ?? 'N/A'],
            ['Newest Log', $stats['newest_bet'] ?? 'N/A']
        ]);

        if ($dryRun) {
            $results = $cleanupService->previewCleanup($days);
            
            if ($results['success']) {
                $data = $results['data'];
                $this->info("📊 DRY RUN RESULTS:");
                $this->info("   Game logs to delete: " . number_format($data['count_to_delete']));
                $this->info("   Cutoff date: " . $data['cutoff_date']->format('Y-m-d H:i:s'));
                
                if ($data['count_to_delete'] > 0) {
                    $this->warn("⚠️  {$data['count_to_delete']} game logs would be deleted");
                    
                    if ($data['sample_bets']->count() > 0) {
                        $this->info("\n📋 Sample Game Logs to be Deleted:");
                        $this->table(['ID', 'User', 'Game', 'Amount', 'Date'], 
                            $data['sample_bets']->map(function($bet) {
                                return [
                                    $bet->id,
                                    $bet->user->user_name ?? 'N/A',
                                    $bet->game_name ?? 'N/A',
                                    number_format($bet->amount ?? 0, 2),
                                    $bet->created_at->format('Y-m-d H:i:s')
                                ];
                            })->toArray()
                        );
                    }
                } else {
                    $this->info("✅ No game logs need cleanup");
                }
            } else {
                $this->error("❌ Preview failed: " . $results['error']);
                return 1;
            }
            
            return 0;
        }

        // Confirm before proceeding
        if (!$this->confirm('Are you sure you want to delete old game logs? This operation cannot be undone.')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info("🚀 Starting cleanup process...");

        // Perform cleanup
        $results = $cleanupService->deleteOldGameLogs($days);

        if ($results['success']) {
            $this->info("✅ Cleanup completed successfully!");
            $this->info("📊 Results:");
            $this->info("   Deleted game logs: " . number_format($results['deleted_count']));
            $this->info("   Duration: " . $results['duration'] . " seconds");
            
            if (!empty($results['errors'])) {
                $this->warn("⚠️  " . count($results['errors']) . " errors occurred during cleanup");
            }

            // Optimize table if requested
            if ($optimize) {
                $this->info("🔧 Optimizing place_bets table...");
                $optimizeResults = $cleanupService->optimizeTable();
                
                if ($optimizeResults['success']) {
                    $this->info("✅ Table optimization completed!");
                    $this->info("   Operations: " . implode(', ', $optimizeResults['operations']));
                    $this->info("   Duration: " . $optimizeResults['duration'] . " seconds");
                } else {
                    $this->error("❌ Table optimization failed: " . $optimizeResults['error']);
                }
            }

            // Show updated stats
            $newStats = $cleanupService->getCleanupStats();
            $this->info("\n📈 Updated Statistics:");
            $this->table(['Metric', 'Before', 'After'], [
                ['Total Game Logs', number_format($stats['total_bets'] ?? 0), number_format($newStats['total_bets'] ?? 0)],
                ['Logs Older Than 15 Days', number_format($stats['bets_older_than_15_days'] ?? 0), number_format($newStats['bets_older_than_15_days'] ?? 0)],
                ['Table Size (MB)', $stats['table_size'] ?? 0, $newStats['table_size'] ?? 0]
            ]);

        } else {
            $this->error("❌ Cleanup failed: " . $results['error']);
            return 1;
        }

        $this->info("\n🎉 Game log cleanup process completed successfully!");
        $this->info("💡 Tip: Run this command daily to keep your database optimized");
        
        return 0;
    }
}