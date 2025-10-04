<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate the GetBalance API response
$user = App\Models\User::where('user_name', 'PLAYER0101')->first();

if ($user) {
    echo "=== DEBUGGING BALANCE RESPONSE ===" . PHP_EOL;
    echo "User: " . $user->user_name . PHP_EOL;
    echo "Raw Balance: " . $user->balance . PHP_EOL;
    echo "Balance Type: " . gettype($user->balance) . PHP_EOL;
    
    // Test different currency conversions
    $currencies = ['IDR', 'IDR2', 'MMK2', 'VND2'];
    
    foreach ($currencies as $currency) {
        echo PHP_EOL . "--- Currency: $currency ---" . PHP_EOL;
        
        $balance = $user->balance;
        
        // Apply currency conversion (same logic as GetBalanceController)
        if (in_array($currency, ['IDR2', 'KRW2', 'MMK2', 'VND2', 'LAK2', 'KHR2'])) {
            $balance = $balance / 1000; // Apply 1:1000 conversion
            $balance = round($balance, 4);
        } else {
            $balance = round($balance, 2);
        }
        
        echo "Converted Balance: " . $balance . PHP_EOL;
        echo "Balance Type: " . gettype($balance) . PHP_EOL;
        
        // Test JSON serialization
        $testResponse = [
            'member_account' => 'PLAYER0101',
            'product_code' => 1006,
            'balance' => $balance,
            'code' => 0,
            'message' => 'Success',
        ];
        
        echo "JSON Response: " . json_encode($testResponse) . PHP_EOL;
    }
    
} else {
    echo "Player PLAYER0101 not found." . PHP_EOL;
}
