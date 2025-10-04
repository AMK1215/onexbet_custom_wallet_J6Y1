<?php

// Debug script to test CustomWalletService
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\CustomWalletService;
use App\Enums\TransactionName;
use App\Enums\UserType;

echo "=== Debugging CustomWalletService ===\n\n";

try {
    $customWalletService = new CustomWalletService;
    
    // Get a test user
    $owner = User::where('type', UserType::Owner->value)->first();
    
    if (!$owner) {
        echo "❌ No owner user found\n";
        exit;
    }
    
    echo "Testing with Owner: {$owner->user_name} (ID: {$owner->id})\n";
    echo "Current balance: " . number_format($owner->balance, 2) . "\n\n";
    
    // Test 1: Simple deposit
    echo "Test 1: Simple deposit of 1000\n";
    $result = $customWalletService->deposit($owner, 1000, TransactionName::CapitalDeposit);
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    $owner->refresh();
    echo "New balance: " . number_format($owner->balance, 2) . "\n\n";
    
    // Test 2: Check if custom transactions table exists
    echo "Test 2: Check custom transactions table\n";
    try {
        $transactions = \App\Models\CustomTransaction::count();
        echo "Custom transactions count: {$transactions}\n";
    } catch (Exception $e) {
        echo "❌ Custom transactions table error: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check database connection
    echo "Test 3: Database connection test\n";
    try {
        $userCount = User::count();
        echo "✅ Database connection OK. User count: {$userCount}\n";
    } catch (Exception $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Check if custom transactions table exists in database
    echo "Test 4: Check if custom_transactions table exists\n";
    try {
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('custom_transactions');
        echo "Custom transactions table exists: " . ($tableExists ? "YES" : "NO") . "\n";
        
        if ($tableExists) {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('custom_transactions');
            echo "Columns: " . implode(', ', $columns) . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Schema check error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
