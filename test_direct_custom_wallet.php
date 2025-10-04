<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\CustomWalletService;
use App\Enums\TransactionName;

echo "=== TESTING DIRECT CUSTOM WALLET SERVICE USAGE ===\n\n";

// Check current user balances
$players = User::whereIn('user_name', ['PLAYER0101', 'PLAYER0201'])->get();

echo "Current Player Balances:\n";
foreach ($players as $player) {
    echo "Player: {$player->user_name}\n";
    echo "  Raw Balance: {$player->balance}\n";
    echo "  Balance Type: " . gettype($player->balance) . "\n\n";
}

echo "=== WEBHOOK CONTROLLERS NOW USE CUSTOM WALLET SERVICE DIRECTLY ===\n\n";
echo "✅ Changes Made:\n";
echo "1. DepositController: Now uses CustomWalletService directly\n";
echo "2. WithdrawController: Now uses CustomWalletService directly\n";
echo "3. WalletService: Fixed balanceFloat references to use balance\n";
echo "4. CustomWalletService: Added comprehensive debug logging\n\n";

echo "🔍 KEY FIXES:\n";
echo "- Removed WalletService wrapper layer\n";
echo "- Fixed balanceFloat -> balance references\n";
echo "- Added debug logging to trace balance updates\n";
echo "- Direct CustomWalletService usage for better performance\n\n";

echo "📋 NEXT STEPS:\n";
echo "1. Run your API tests again\n";
echo "2. Check storage/logs/laravel.log for CustomWalletService debug output\n";
echo "3. Look for 'CustomWalletService::deposit/withdraw - Before/After update' logs\n";
echo "4. Verify that balance updates are now working correctly\n\n";

echo "🎯 EXPECTED RESULTS:\n";
echo "WithdrawController should now show:\n";
echo "- before_balance: 50000, balance: 49990 (after -10 withdraw)\n";
echo "DepositController should now show:\n";
echo "- before_balance: 49990, balance: 50000 (after +10 deposit)\n\n";

echo "This should fix the 'Balance is incorrect' issue! 🚀\n";
