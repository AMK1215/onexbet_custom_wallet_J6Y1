<?php

namespace App\Console\Commands;

use App\Services\TransactionArchiveService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ArchiveOldTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:archive 
                            {--months=12 : Number of months old transactions to archive}
                            {--dry-run : Show what would be archived without actually doing it}
                            {--optimize : Optimize the main table after archiving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive old transactions to separate table (SAFE alternative to truncating)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->option('months');
        $dryRun = $this->option('dry-run');
        $optimize = $this->option('optimize');

        $this->info("🔄 Starting transaction archive process...");
        $this->info("📅 Archiving transactions older than {$months} months");

        if ($dryRun) {
            $this->warn("🧪 DRY RUN MODE - No actual changes will be made");
        }

        // Show current stats
        $archiveService = new TransactionArchiveService();
        $stats = $archiveService->getArchiveStats();

        $this->table(['Metric', 'Count'], [
            ['Main Table Transactions', number_format($stats['main_table_count'] ?? 0)],
            ['Archived Transactions', number_format($stats['archive_table_count'] ?? 0)],
            ['Main Table Size (MB)', $stats['main_table_size'] ?? 0],
            ['Archive Table Size (MB)', $stats['archive_table_size'] ?? 0],
            ['Oldest Transaction', $stats['oldest_transaction'] ?? 'N/A'],
            ['Newest Transaction', $stats['newest_transaction'] ?? 'N/A']
        ]);

        if ($dryRun) {
            $cutoffDate = Carbon::now()->subMonths($months);
            $countToArchive = \App\Models\CustomTransaction::where('created_at', '<', $cutoffDate)->count();
            
            $this->info("📊 DRY RUN RESULTS:");
            $this->info("   Transactions to archive: " . number_format($countToArchive));
            $this->info("   Cutoff date: " . $cutoffDate->format('Y-m-d H:i:s'));
            
            if ($countToArchive > 0) {
                $this->warn("⚠️  {$countToArchive} transactions would be archived");
            } else {
                $this->info("✅ No transactions need archiving");
            }
            
            return 0;
        }

        // Confirm before proceeding
        if (!$this->confirm('Do you want to proceed with archiving old transactions?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info("🚀 Starting archive process...");

        // Perform archiving
        $results = $archiveService->archiveOldTransactions($months);

        if ($results['success']) {
            $this->info("✅ Archive completed successfully!");
            $this->info("📊 Results:");
            $this->info("   Archived transactions: " . number_format($results['archived_count']));
            $this->info("   Deleted from main table: " . number_format($results['deleted_from_main'] ?? 0));
            $this->info("   Duration: " . $results['duration'] . " seconds");
            
            if (!empty($results['errors'])) {
                $this->warn("⚠️  " . count($results['errors']) . " errors occurred during archiving");
            }

            // Optimize table if requested
            if ($optimize) {
                $this->info("🔧 Optimizing main table...");
                $optimizeResults = $archiveService->optimizeMainTable();
                
                if ($optimizeResults['success']) {
                    $this->info("✅ Table optimization completed!");
                    $this->info("   Operations: " . implode(', ', $optimizeResults['operations']));
                    $this->info("   Duration: " . $optimizeResults['duration'] . " seconds");
                } else {
                    $this->error("❌ Table optimization failed: " . $optimizeResults['error']);
                }
            }

            // Show updated stats
            $newStats = $archiveService->getArchiveStats();
            $this->info("\n📈 Updated Statistics:");
            $this->table(['Metric', 'Before', 'After'], [
                ['Main Table Transactions', number_format($stats['main_table_count'] ?? 0), number_format($newStats['main_table_count'] ?? 0)],
                ['Archived Transactions', number_format($stats['archive_table_count'] ?? 0), number_format($newStats['archive_table_count'] ?? 0)],
                ['Main Table Size (MB)', $stats['main_table_size'] ?? 0, $newStats['main_table_size'] ?? 0]
            ]);

        } else {
            $this->error("❌ Archive failed: " . $results['error']);
            return 1;
        }

        $this->info("\n🎉 Transaction archive process completed successfully!");
        $this->info("💡 Tip: Run this command monthly to keep your main table optimized");
        
        return 0;
    }
}