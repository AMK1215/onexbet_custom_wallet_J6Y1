<?php

// Script to reset users and run the updated seeder
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CustomTransaction;
use Illuminate\Support\Facades\DB;

echo "=== Resetting Users and Running Updated Seeder ===\n\n";

try {
    DB::beginTransaction();
    
    echo "1. Clearing existing users...\n";
    $userCount = User::count();
    echo "Found {$userCount} existing users\n";
    
    // Clear custom transactions first (foreign key constraint)
    CustomTransaction::truncate();
    echo "✅ Cleared custom transactions\n";
    
    // Clear users
    User::truncate();
    echo "✅ Cleared all users\n";
    
    DB::commit();
    
    echo "\n2. Running updated UsersTableSeeder...\n";
    
    // Run the seeder
    $seeder = new \Database\Seeders\UsersTableSeeder();
    $seeder->run();
    
    echo "\n3. Checking final balances...\n";
    
    // Check final balances
    $users = User::select('id', 'name', 'user_name', 'type', 'balance')
        ->orderBy('type')
        ->orderBy('id')
        ->get();
    
    echo "\n📊 Final User Balances:\n";
    $totalBalance = 0;
    
    foreach ($users as $user) {
        $typeName = \App\Enums\UserType::from($user->type)->name;
        $balance = number_format($user->balance, 2);
        echo "ID: {$user->id} | {$typeName} | {$user->user_name} | Balance: {$balance}\n";
        $totalBalance += $user->balance;
    }
    
    echo "\n=== Summary ===\n";
    echo "Total Users: {$users->count()}\n";
    echo "Total Balance: " . number_format($totalBalance, 2) . "\n";
    echo "Average Balance: " . number_format($totalBalance / $users->count(), 2) . "\n";
    
    echo "\n✅ Reset and seeding completed successfully!\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
