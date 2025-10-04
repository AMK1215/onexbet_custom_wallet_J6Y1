<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING DECIMAL FORMATTING ===" . PHP_EOL;

// Test the new formatting logic
$testValues = [
    'IDR' => 50000,
    'IDR2' => 50,
    'MMK2' => 50,
    'VND2' => 50
];

foreach ($testValues as $currency => $balance) {
    echo PHP_EOL . "--- Currency: $currency ---" . PHP_EOL;
    echo "Input Balance: " . $balance . PHP_EOL;
    
    // Apply the new formatting logic
    $specialCurrencies = ['IDR2', 'KRW2', 'MMK2', 'VND2', 'LAK2', 'KHR2'];
    $decimalPlaces = in_array($currency, $specialCurrencies) ? 4 : 2;
    $balanceValue = round($balance, $decimalPlaces);
    
    // Format the float value to the correct precision string, then cast back to float.
    $formattedBalance = match($decimalPlaces) {
        2 => (float) sprintf('%.2f', $balanceValue),
        4 => (float) sprintf('%.4f', $balanceValue),
        default => (float) $balanceValue,
    };
    
    echo "Decimal Places: " . $decimalPlaces . PHP_EOL;
    echo "Formatted Balance: " . $formattedBalance . PHP_EOL;
    echo "Formatted Type: " . gettype($formattedBalance) . PHP_EOL;
    
    // Test JSON serialization
    $testResponse = [
        'member_account' => 'PLAYER0101',
        'product_code' => 1006,
        'balance' => $formattedBalance,
        'code' => 0,
        'message' => 'Success',
    ];
    
    echo "JSON Response: " . json_encode($testResponse) . PHP_EOL;
}
