<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_custom_transactions');
    }
};