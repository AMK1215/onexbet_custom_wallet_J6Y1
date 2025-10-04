<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test balance calculation for IDR currency
$user = App\Models\User::where('user_name', 'PLAYER0101')->first();
if ($user) {
    echo "User: " . $user->user_name . PHP_EOL;
    echo "Current Balance: " . $user->balance . PHP_EOL;
    
    // Test currency conversion for IDR
    $currency = 'IDR';
    $betAmount = 10.0;
    
    // Simulate the conversion logic from WithdrawController
    $divisor = match ($currency) {
        'IDR2' => 100,
        'KRW2' => 10,
        'MMK2' => 1000,
        'VND2' => 1000,
        'LAK2' => 10,
        'KHR2' => 100,
        default => 1,
    };
    
    $convertedAmount = abs(round($betAmount * $divisor, 4));
    echo "Bet Amount: " . $betAmount . PHP_EOL;
    echo "Divisor: " . $divisor . PHP_EOL;
    echo "Converted Amount: " . $convertedAmount . PHP_EOL;
    
    if ($user->balance >= $convertedAmount) {
        echo "✅ Sufficient balance - bet should succeed" . PHP_EOL;
    } else {
        echo "❌ Insufficient balance - bet should fail" . PHP_EOL;
    }
    
    // Test with the problematic amount
    $problematicBet = 44980.0;
    $convertedProblematic = abs(round($problematicBet * $divisor, 4));
    echo PHP_EOL . "Problematic Bet Amount: " . $problematicBet . PHP_EOL;
    echo "Converted Problematic Amount: " . $convertedProblematic . PHP_EOL;
    
    if ($user->balance >= $convertedProblematic) {
        echo "✅ Sufficient balance for problematic bet" . PHP_EOL;
    } else {
        echo "❌ Insufficient balance for problematic bet (this is correct!)" . PHP_EOL;
    }
    
} else {
    echo "User not found" . PHP_EOL;
}
