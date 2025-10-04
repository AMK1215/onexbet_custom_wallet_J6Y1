<?php

// Script to fix user balances using CustomWalletService
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\CustomWalletService;
use App\Services\WalletService;
use App\Enums\TransactionName;
use App\Enums\UserType;

echo "=== Fixing User Balances ===\n\n";

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
        echo "❌ Missing required users (Owner, SystemWallet, or Master)\n";
        exit;
    }

    echo "📊 Found users:\n";
    echo "- Owner: {$owner->user_name} (ID: {$owner->id})\n";
    echo "- SystemWallet: {$systemWallet->user_name} (ID: {$systemWallet->id})\n";
    echo "- Master: {$master->user_name} (ID: {$master->id})\n";
    echo "- Agents: {$agents->count()}\n";
    echo "- Players: {$players->count()}\n\n";

    // 1. Add initial capital to Owner
    echo "1. Adding initial capital to Owner...\n";
    $ownerDeposit = 500_000_00000000; // 50 trillion
    $result = $walletService->deposit($owner, $ownerDeposit, TransactionName::CapitalDeposit);
    echo $result ? "✅ Owner deposit successful\n" : "❌ Owner deposit failed\n";

    // 2. Add capital to SystemWallet
    echo "2. Adding capital to SystemWallet...\n";
    $systemDeposit = 500 * 100_0000; // 500 million
    $result = $walletService->deposit($systemWallet, $systemDeposit, TransactionName::CapitalDeposit);
    echo $result ? "✅ SystemWallet deposit successful\n" : "❌ SystemWallet deposit failed\n";

    // 3. Transfer from Owner to Master
    echo "3. Transferring from Owner to Master...\n";
    $masterTransfer = 500_000; // 500k
    $result = $walletService->transfer($owner, $master, $masterTransfer, TransactionName::CreditTransfer);
    echo $result ? "✅ Master transfer successful\n" : "❌ Master transfer failed\n";

    // 4. Transfer from Master to Agents
    echo "4. Transferring from Master to Agents...\n";
    foreach ($agents as $index => $agent) {
        $agentTransfer = rand(1, 2) * 100_000; // 100k-200k
        $result = $walletService->transfer($master, $agent, $agentTransfer, TransactionName::CreditTransfer);
        echo $result ? "✅ Agent {$agent->user_name} transfer successful ({$agentTransfer})\n" : "❌ Agent {$agent->user_name} transfer failed\n";
    }

    // 5. Transfer from Agents to Players
    echo "5. Transferring from Agents to Players...\n";
    $playersPerAgent = 4;
    foreach ($agents as $agentIndex => $agent) {
        $agentPlayers = $players->skip($agentIndex * $playersPerAgent)->take($playersPerAgent);
        
        foreach ($agentPlayers as $player) {
            $playerTransfer = 10000; // 10k
            $result = $walletService->transfer($agent, $player, $playerTransfer, TransactionName::CreditTransfer);
            echo $result ? "✅ Player {$player->user_name} transfer successful\n" : "❌ Player {$player->user_name} transfer failed\n";
        }
    }

    echo "\n=== Final Balance Check ===\n";
    
    // Refresh all users and show final balances
    $owner->refresh();
    $systemWallet->refresh();
    $master->refresh();
    $agents = User::where('type', UserType::Agent->value)->get();
    $players = User::where('type', UserType::Player->value)->get();

    echo "Owner ({$owner->user_name}): " . number_format($owner->balance, 2) . "\n";
    echo "SystemWallet ({$systemWallet->user_name}): " . number_format($systemWallet->balance, 2) . "\n";
    echo "Master ({$master->user_name}): " . number_format($master->balance, 2) . "\n";
    
    $totalAgentBalance = $agents->sum('balance');
    echo "Agents Total: " . number_format($totalAgentBalance, 2) . "\n";
    
    $totalPlayerBalance = $players->sum('balance');
    echo "Players Total: " . number_format($totalPlayerBalance, 2) . "\n";
    
    $grandTotal = $owner->balance + $systemWallet->balance + $master->balance + $totalAgentBalance + $totalPlayerBalance;
    echo "Grand Total: " . number_format($grandTotal, 2) . "\n";

    echo "\n✅ Balance fixing completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
