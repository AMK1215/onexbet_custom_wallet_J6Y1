<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== BALANCE COMPARISON TEST ===" . PHP_EOL;

$user = App\Models\User::where('user_name', 'PLAYER0101')->first();
if ($user) {
    echo "User: " . $user->user_name . PHP_EOL;
    echo "Raw Balance: " . $user->balance . PHP_EOL;
    
    // Test what we're currently returning
    $currencies = ['IDR', 'IDR2'];
    
    foreach ($currencies as $currency) {
        echo PHP_EOL . "--- Currency: $currency ---" . PHP_EOL;
        
        $balance = $user->balance;
        
        // Apply our current conversion logic
        if (in_array($currency, ['IDR2', 'KRW2', 'MMK2', 'VND2', 'LAK2', 'KHR2'])) {
            $balance = $balance / 1000; // Apply 1:1000 conversion
            $balance = round($balance, 4);
        } else {
            $balance = round($balance, 2);
        }
        
        echo "Our Current Response: " . $balance . PHP_EOL;
        
        // Test different possible values the provider might expect
        echo "Possible Expected Values:" . PHP_EOL;
        echo "  - Raw balance: " . $user->balance . PHP_EOL;
        echo "  - Divided by 100: " . ($user->balance / 100) . PHP_EOL;
        echo "  - Divided by 1000: " . ($user->balance / 1000) . PHP_EOL;
        echo "  - Divided by 10000: " . ($user->balance / 10000) . PHP_EOL;
    }
    
} else {
    echo "Player PLAYER0101 not found." . PHP_EOL;
}
