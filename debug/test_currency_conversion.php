<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test currency conversion logic
$currencies = ['IDR', 'IDR2', 'MMK', 'MMK2'];

foreach ($currencies as $currency) {
    $specialCurrencies = ['IDR2', 'KRW2', 'MMK2', 'VND2', 'LAK2', 'KHR2'];
    
    $divisor = match ($currency) {
        'IDR2' => 100,
        'KRW2' => 10,
        'MMK2' => 1000,
        'VND2' => 1000,
        'LAK2' => 10,
        'KHR2' => 100,
        default => 1,
    };
    
    $precision = in_array($currency, $specialCurrencies) ? 4 : 2;
    
    echo "Currency: $currency" . PHP_EOL;
    echo "Divisor: $divisor" . PHP_EOL;
    echo "Precision: $precision" . PHP_EOL;
    echo "Is Special: " . (in_array($currency, $specialCurrencies) ? 'Yes' : 'No') . PHP_EOL;
    echo "---" . PHP_EOL;
}

// Test with actual values
$userBalance = 5000;
$betAmount = 10;

echo "User Balance: $userBalance" . PHP_EOL;
echo "Bet Amount: $betAmount" . PHP_EOL;

// For IDR (regular currency)
$currency = 'IDR';
$divisor = 1;
$precision = 2;

$convertedBetAmount = abs(round($betAmount * $divisor, $precision));
echo "Converted Bet Amount for $currency: $convertedBetAmount" . PHP_EOL;

if ($userBalance >= $convertedBetAmount) {
    echo "✅ Sufficient balance" . PHP_EOL;
} else {
    echo "❌ Insufficient balance" . PHP_EOL;
}
