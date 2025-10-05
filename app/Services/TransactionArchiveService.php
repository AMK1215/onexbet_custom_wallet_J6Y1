<?php

namespace App\Services;

use App\Models\CustomTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionArchiveService
{
    /**
     * Archive old transactions to separate table (SAFE method)
     */
    public function archiveOldTransactions(int $monthsOld = 12): array
    {
        $cutoffDate = Carbon::now()->subMonths($monthsOld);
        $results = [
            'archived_count' => 0,
            'errors' => [],
            'start_time' => now(),
            'cutoff_date' => $cutoffDate
        ];

        try {
            DB::transaction(function () use ($cutoffDate, &$results) {
                // 1. Create archive table if it doesn't exist
                $this->createArchiveTable();

                // 2. Count transactions to be archived
                $countToArchive = CustomTransaction::where('created_at', '<', $cutoffDate)->count();
                $results['total_to_archive'] = $countToArchive;

                if ($countToArchive === 0) {
                    Log::info('No transactions found to archive');
                    return;
                }

                // 3. Archive in chunks to avoid memory issues
                CustomTransaction::where('created_at', '<', $cutoffDate)
                    ->chunkById(1000, function ($transactions) use (&$results) {
                        foreach ($transactions as $transaction) {
                            try {
                                // Insert into archive table
                                DB::table('archived_custom_transactions')->insert([
                                    'original_id' => $transaction->id,
                                    'user_id' => $transaction->user_id,
                                    'target_user_id' => $transaction->target_user_id,
                                    'amount' => $transaction->amount,
                                    'type' => $transaction->type,
                                    'transaction_name' => $transaction->transaction_name,
                                    'old_balance' => $transaction->old_balance,
                                    'new_balance' => $transaction->new_balance,
                                    'meta' => $transaction->meta,
                                    'uuid' => $transaction->uuid,
                                    'confirmed' => $transaction->confirmed,
                                    'deleted_at' => $transaction->deleted_at,
                                    'deleted_by' => $transaction->deleted_by,
                                    'deleted_reason' => $transaction->deleted_reason,
                                    'created_at' => $transaction->created_at,
                                    'updated_at' => $transaction->updated_at,
                                    'archived_at' => now(),
                                    'archive_batch_id' => $results['batch_id'] ?? uniqid()
                                ]);

                                $results['archived_count']++;
                            } catch (\Exception $e) {
                                $results['errors'][] = [
                                    'transaction_id' => $transaction->id,
                                    'error' => $e->getMessage()
                                ];
                            }
                        }
                    });

                // 4. Delete archived transactions from main table
                $deletedCount = CustomTransaction::where('created_at', '<', $cutoffDate)->delete();
                $results['deleted_from_main'] = $deletedCount;

                // 5. Log the operation
                Log::info('Transaction archive completed', [
                    'archived_count' => $results['archived_count'],
                    'deleted_from_main' => $deletedCount,
                    'cutoff_date' => $cutoffDate,
                    'errors_count' => count($results['errors'])
                ]);
            });

            $results['success'] = true;
            $results['duration'] = now()->diffInSeconds($results['start_time']);

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            Log::error('Transaction archive failed', [
                'error' => $e->getMessage(),
                'cutoff_date' => $cutoffDate
            ]);
        }

        return $results;
    }

    /**
     * Create archive table if it doesn't exist
     */
    private function createArchiveTable(): void
    {
        // Check if we're using PostgreSQL
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL syntax
            $sql = "
                CREATE TABLE IF NOT EXISTS archived_custom_transactions (
                    id BIGSERIAL PRIMARY KEY,
                    original_id BIGINT,
                    user_id BIGINT,
                    target_user_id BIGINT,
                    amount DECIMAL(64,2),
                    type VARCHAR(255),
                    transaction_name VARCHAR(255),
                    old_balance DECIMAL(64,2),
                    new_balance DECIMAL(64,2),
                    meta JSONB,
                    uuid VARCHAR(255),
                    confirmed BOOLEAN DEFAULT TRUE,
                    deleted_at TIMESTAMP NULL,
                    deleted_by BIGINT NULL,
                    deleted_reason TEXT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    archive_batch_id VARCHAR(255)
                )
            ";
        } else {
            // MySQL syntax
            $sql = "
                CREATE TABLE IF NOT EXISTS archived_custom_transactions (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    original_id BIGINT UNSIGNED,
                    user_id BIGINT UNSIGNED,
                    target_user_id BIGINT UNSIGNED,
                    amount DECIMAL(64,2),
                    type VARCHAR(255),
                    transaction_name VARCHAR(255),
                    old_balance DECIMAL(64,2),
                    new_balance DECIMAL(64,2),
                    meta JSON,
                    uuid VARCHAR(255),
                    confirmed BOOLEAN DEFAULT TRUE,
                    deleted_at TIMESTAMP NULL,
                    deleted_by BIGINT UNSIGNED NULL,
                    deleted_reason TEXT NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    archive_batch_id VARCHAR(255),
                    INDEX idx_original_id (original_id),
                    INDEX idx_user_id (user_id),
                    INDEX idx_target_user_id (target_user_id),
                    INDEX idx_archived_at (archived_at),
                    INDEX idx_archive_batch_id (archive_batch_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
        }

        DB::statement($sql);
        
        // Create indexes for PostgreSQL
        if ($driver === 'pgsql') {
            $indexes = [
                'CREATE INDEX IF NOT EXISTS idx_archived_original_id ON archived_custom_transactions(original_id)',
                'CREATE INDEX IF NOT EXISTS idx_archived_user_id ON archived_custom_transactions(user_id)',
                'CREATE INDEX IF NOT EXISTS idx_archived_target_user_id ON archived_custom_transactions(target_user_id)',
                'CREATE INDEX IF NOT EXISTS idx_archived_archived_at ON archived_custom_transactions(archived_at)',
                'CREATE INDEX IF NOT EXISTS idx_archived_batch_id ON archived_custom_transactions(archive_batch_id)',
                'CREATE INDEX IF NOT EXISTS idx_archived_user_type ON archived_custom_transactions(user_id, type)',
                'CREATE INDEX IF NOT EXISTS idx_archived_target_type ON archived_custom_transactions(target_user_id, type)',
                'CREATE INDEX IF NOT EXISTS idx_archived_created_at ON archived_custom_transactions(created_at)'
            ];
            
            foreach ($indexes as $index) {
                try {
                    DB::statement($index);
                } catch (\Exception $e) {
                    // Index might already exist, ignore error
                }
            }
        }
    }

    /**
     * Get archive statistics
     */
    public function getArchiveStats(): array
    {
        try {
            $stats = [
                'main_table_count' => CustomTransaction::count(),
                'archive_table_count' => DB::table('archived_custom_transactions')->count(),
                'main_table_size' => $this->getTableSize('custom_transactions'),
                'archive_table_size' => $this->getTableSize('archived_custom_transactions'),
                'oldest_transaction' => CustomTransaction::min('created_at'),
                'newest_transaction' => CustomTransaction::max('created_at'),
                'oldest_archived' => DB::table('archived_custom_transactions')->min('created_at'),
                'newest_archived' => DB::table('archived_custom_transactions')->max('created_at')
            ];

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get archive stats', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Restore archived transactions (emergency use only)
     */
    public function restoreArchivedTransactions(string $batchId = null): array
    {
        $results = [
            'restored_count' => 0,
            'errors' => [],
            'start_time' => now()
        ];

        try {
            DB::transaction(function () use ($batchId, &$results) {
                $query = DB::table('archived_custom_transactions');
                
                if ($batchId) {
                    $query->where('archive_batch_id', $batchId);
                }

                $archivedTransactions = $query->get();

                foreach ($archivedTransactions as $archived) {
                    try {
                        // Restore to main table
                        CustomTransaction::create([
                            'user_id' => $archived->user_id,
                            'target_user_id' => $archived->target_user_id,
                            'amount' => $archived->amount,
                            'type' => $archived->type,
                            'transaction_name' => $archived->transaction_name,
                            'old_balance' => $archived->old_balance,
                            'new_balance' => $archived->new_balance,
                            'meta' => $archived->meta,
                            'uuid' => $archived->uuid,
                            'confirmed' => $archived->confirmed,
                            'deleted_at' => $archived->deleted_at,
                            'deleted_by' => $archived->deleted_by,
                            'deleted_reason' => $archived->deleted_reason,
                            'created_at' => $archived->created_at,
                            'updated_at' => $archived->updated_at
                        ]);

                        $results['restored_count']++;
                    } catch (\Exception $e) {
                        $results['errors'][] = [
                            'archived_id' => $archived->id,
                            'error' => $e->getMessage()
                        ];
                    }
                }

                // Delete from archive table
                if ($batchId) {
                    DB::table('archived_custom_transactions')->where('archive_batch_id', $batchId)->delete();
                } else {
                    DB::table('archived_custom_transactions')->delete();
                }

                Log::warning('Archived transactions restored', [
                    'restored_count' => $results['restored_count'],
                    'batch_id' => $batchId
                ]);
            });

            $results['success'] = true;
            $results['duration'] = now()->diffInSeconds($results['start_time']);

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            Log::error('Transaction restore failed', ['error' => $e->getMessage()]);
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
     * Optimize main transactions table
     */
    public function optimizeMainTable(): array
    {
        $results = [
            'start_time' => now(),
            'operations' => []
        ];

        try {
            $driver = DB::getDriverName();
            
            if ($driver === 'pgsql') {
                // PostgreSQL optimization
                // 1. Analyze table
                DB::statement('ANALYZE custom_transactions');
                $results['operations'][] = 'Table analyzed';

                // 2. Reindex table (PostgreSQL equivalent of optimize)
                DB::statement('REINDEX TABLE custom_transactions');
                $results['operations'][] = 'Table reindexed';

                // 3. Vacuum table
                DB::statement('VACUUM custom_transactions');
                $results['operations'][] = 'Table vacuumed';

            } else {
                // MySQL optimization
                // 1. Analyze table
                DB::statement('ANALYZE TABLE custom_transactions');
                $results['operations'][] = 'Table analyzed';

                // 2. Optimize table
                DB::statement('OPTIMIZE TABLE custom_transactions');
                $results['operations'][] = 'Table optimized';

                // 3. Check and repair if needed
                $checkResult = DB::select('CHECK TABLE custom_transactions');
                if ($checkResult[0]->Msg_text !== 'OK') {
                    DB::statement('REPAIR TABLE custom_transactions');
                    $results['operations'][] = 'Table repaired';
                }
            }

            $results['success'] = true;
            $results['duration'] = now()->diffInSeconds($results['start_time']);

            Log::info('Main transactions table optimized', $results);

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            Log::error('Table optimization failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }
}
