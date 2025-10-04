<?php

// Simple script to fix user balances with reasonable amounts
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\CustomWalletService;
use App\Services\WalletService;
use App\Enums\TransactionName;
use App\Enums\UserType;

echo "=== Fixing User Balances (Simple) ===\n\n";

try {
    $customWalletService = new CustomWalletService;
    $walletService = new WalletService($customWalletService);

    // Get existing users
    $owner = User::where('type', UserType::Owner->value)->first();
    $systemWallet = User::where('type', UserType::SystemWallet->value)->first();
    $master = User::where('type', UserType::Master->value)->first();
    $agents = User::where('type', UserType::Agent->value)->get();
    $players = User::where('type', UserType::Player->value)->get();

    if (!$owner || !$systemWallet || !$master) {
        echo "❌ Missing required users\n";
        exit;
    }

    echo "📊 Current balances:\n";
    echo "- Owner: " . number_format($owner->balance, 2) . "\n";
    echo "- SystemWallet: " . number_format($systemWallet->balance, 2) . "\n";
    echo "- Master: " . number_format($master->balance, 2) . "\n\n";

    // 1. Add reasonable capital to Owner (1 million)
    echo "1. Adding 1,000,000 to Owner...\n";
    $result = $walletService->deposit($owner, 1000000, TransactionName::CapitalDeposit);
    echo $result ? "✅ Owner deposit successful\n" : "❌ Owner deposit failed\n";

    // 2. Transfer from Owner to Master (100k)
    echo "2. Transferring 100,000 from Owner to Master...\n";
    $result = $walletService->transfer($owner, $master, 100000, TransactionName::CreditTransfer);
    echo $result ? "✅ Master transfer successful\n" : "❌ Master transfer failed\n";

    // 3. Transfer from Master to Agents (20k each)
    echo "3. Transferring 20,000 from Master to each Agent...\n";
    foreach ($agents as $agent) {
        $result = $walletService->transfer($master, $agent, 20000, TransactionName::CreditTransfer);
        echo $result ? "✅ Agent {$agent->user_name} transfer successful\n" : "❌ Agent {$agent->user_name} transfer failed\n";
    }

    // 4. Transfer from Agents to Players (2k each)
    echo "4. Transferring 2,000 from each Agent to their Players...\n";
    $playersPerAgent = 4;
    foreach ($agents as $agentIndex => $agent) {
        $agentPlayers = $players->skip($agentIndex * $playersPerAgent)->take($playersPerAgent);
        
        foreach ($agentPlayers as $player) {
            $result = $walletService->transfer($agent, $player, 2000, TransactionName::CreditTransfer);
            echo $result ? "✅ Player {$player->user_name} transfer successful\n" : "❌ Player {$player->user_name} transfer failed\n";
        }
    }

    echo "\n=== Final Balance Check ===\n";
    
    // Refresh and show final balances
    $owner->refresh();
    $systemWallet->refresh();
    $master->refresh();
    $agents = User::where('type', UserType::Agent->value)->get();
    $players = User::where('type', UserType::Player->value)->get();

    echo "Owner ({$owner->user_name}): " . number_format($owner->balance, 2) . "\n";
    echo "SystemWallet ({$systemWallet->user_name}): " . number_format($systemWallet->balance, 2) . "\n";
    echo "Master ({$master->user_name}): " . number_format($master->balance, 2) . "\n";
    
    echo "\nAgents:\n";
    foreach ($agents as $agent) {
        echo "- {$agent->user_name}: " . number_format($agent->balance, 2) . "\n";
    }
    
    echo "\nPlayers:\n";
    foreach ($players as $player) {
        echo "- {$player->user_name}: " . number_format($player->balance, 2) . "\n";
    }
    
    $totalBalance = $owner->balance + $systemWallet->balance + $master->balance + $agents->sum('balance') + $players->sum('balance');
    echo "\nGrand Total: " . number_format($totalBalance, 2) . "\n";

    echo "\n✅ Balance fixing completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
