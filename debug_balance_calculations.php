<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== DEBUGGING BALANCE CALCULATIONS ===\n\n";

// Test with PLAYER0101
$player = User::where('user_name', 'PLAYER0101')->first();

if ($player) {
    echo "Player: " . $player->user_name . "\n";
    echo "Raw Balance: " . $player->balance . "\n";
    echo "Balance Type: " . gettype($player->balance) . "\n\n";
    
    // Test different currency conversions
    $currencies = ['IDR', 'IDR2', 'MMK2', 'VND2', 'KRW2', 'LAK2', 'KHR2'];
    
    foreach ($currencies as $currency) {
        $balance = $player->balance;
        
        // Get currency conversion value
        $currencyValue = match ($currency) {
            'IDR2' => 100,
            'KRW2' => 10,
            'MMK2' => 1000,
            'VND2' => 1000,
            'LAK2' => 10,
            'KHR2' => 100,
            default => 1,
        };
        
        // Apply conversion
        $convertedBalance = $balance / $currencyValue;
        
        // Round to 4 decimal places for special currencies, 2 for regular
        $specialCurrencies = ['IDR2', 'KRW2', 'MMK2', 'VND2', 'LAK2', 'KHR2'];
        if (in_array($currency, $specialCurrencies)) {
            $finalBalance = round($convertedBalance, 4);
        } else {
            $finalBalance = round($convertedBalance, 2);
        }
        
        echo "Currency: $currency\n";
        echo "  Raw Balance: $balance\n";
        echo "  Currency Value: $currencyValue\n";
        echo "  Converted: $convertedBalance\n";
        echo "  Final (rounded): $finalBalance\n";
        echo "  JSON: " . json_encode($finalBalance) . "\n\n";
    }
    
    // Test what the working site should return
    echo "=== EXPECTED WORKING SITE VALUES ===\n";
    echo "For IDR2 (balance 50000): " . json_encode(round(50000 / 100, 4)) . "\n";
    echo "For IDR (balance 50000): " . json_encode(round(50000 / 1, 2)) . "\n";
    echo "For MMK2 (balance 50000): " . json_encode(round(50000 / 1000, 4)) . "\n";
    
} else {
    echo "Player PLAYER0101 not found.\n";
}
