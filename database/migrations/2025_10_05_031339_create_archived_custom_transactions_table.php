<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if we're using PostgreSQL
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL - Use raw SQL for better control
            DB::statement('
                CREATE TABLE archived_custom_transactions (
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
            ');
            
            // Create indexes for PostgreSQL
            $indexes = [
                'CREATE INDEX idx_archived_original_id ON archived_custom_transactions(original_id)',
                'CREATE INDEX idx_archived_user_id ON archived_custom_transactions(user_id)',
                'CREATE INDEX idx_archived_target_user_id ON archived_custom_transactions(target_user_id)',
                'CREATE INDEX idx_archived_archived_at ON archived_custom_transactions(archived_at)',
                'CREATE INDEX idx_archived_batch_id ON archived_custom_transactions(archive_batch_id)',
                'CREATE INDEX idx_archived_user_type ON archived_custom_transactions(user_id, type)',
                'CREATE INDEX idx_archived_target_type ON archived_custom_transactions(target_user_id, type)',
                'CREATE INDEX idx_archived_created_at ON archived_custom_transactions(created_at)'
            ];
            
            foreach ($indexes as $index) {
                DB::statement($index);
            }
        } else {
            // MySQL - Use Laravel Schema Builder
            Schema::create('archived_custom_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('original_id')->nullable(); // Original transaction ID
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('target_user_id')->nullable();
                $table->decimal('amount', 64, 2);
                $table->string('type');
                $table->string('transaction_name');
                $table->decimal('old_balance', 64, 2);
                $table->decimal('new_balance', 64, 2);
                $table->json('meta')->nullable();
                $table->string('uuid')->nullable();
                $table->boolean('confirmed')->default(true);
                $table->timestamp('deleted_at')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->text('deleted_reason')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('archived_at')->useCurrent(); // When it was archived
                $table->string('archive_batch_id')->nullable(); // Batch identifier for grouping

                // Indexes for performance
                $table->index('original_id');
                $table->index('user_id');
                $table->index('target_user_id');
                $table->index('archived_at');
                $table->index('archive_batch_id');
                $table->index(['user_id', 'type']);
                $table->index(['target_user_id', 'type']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_custom_transactions');
    }
};