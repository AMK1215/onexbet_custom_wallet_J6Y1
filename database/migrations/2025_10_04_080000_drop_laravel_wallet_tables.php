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
        // Drop Laravel Wallet package tables in correct order (respecting foreign keys)
        
        // Drop transfers table first (has foreign keys to transactions)
        Schema::dropIfExists('transfers');
        
        // Drop transactions table (has foreign keys to wallets)
        Schema::dropIfExists('transactions');
        
        // Drop wallets table last
        Schema::dropIfExists('wallets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't recreate these tables as we're permanently removing Laravel Wallet
        // If you need to rollback, you would need to restore from backup
        $this->command->warn('Laravel Wallet tables have been permanently removed. Rollback not supported.');
    }
};
