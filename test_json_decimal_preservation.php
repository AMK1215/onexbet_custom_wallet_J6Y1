<?php

echo "=== TESTING JSON DECIMAL PRESERVATION ===" . PHP_EOL;

$testValues = [
    'IDR' => 50000,
    'IDR2' => 50,
];

foreach ($testValues as $currency => $balance) {
    echo PHP_EOL . "--- Currency: $currency ---" . PHP_EOL;
    echo "Input Balance: " . $balance . PHP_EOL;
    
    $decimalPlaces = ($currency === 'IDR') ? 2 : 4;
    
    // Test different approaches
    echo PHP_EOL . "Testing different approaches:" . PHP_EOL;
    
    // Approach 1: sprintf + float cast
    $approach1 = (float) sprintf('%.' . $decimalPlaces . 'f', $balance);
    echo "1. sprintf + float: " . $approach1 . " -> JSON: " . json_encode($approach1) . PHP_EOL;
    
    // Approach 2: number_format + float cast
    $approach2 = (float) number_format($balance, $decimalPlaces, '.', '');
    echo "2. number_format + float: " . $approach2 . " -> JSON: " . json_encode($approach2) . PHP_EOL;
    
    // Approach 3: Direct string (not recommended but for testing)
    $approach3 = sprintf('%.' . $decimalPlaces . 'f', $balance);
    echo "3. sprintf string: " . $approach3 . " -> JSON: " . json_encode($approach3) . PHP_EOL;
    
    // Approach 4: Using round with precision
    $approach4 = round($balance, $decimalPlaces);
    echo "4. round only: " . $approach4 . " -> JSON: " . json_encode($approach4) . PHP_EOL;
    
    // Test full response
    $response = [
        'member_account' => 'PLAYER0101',
        'product_code' => 1006,
        'balance' => $approach1,
        'code' => 0,
        'message' => 'Success',
    ];
    
    echo "Full JSON Response: " . json_encode($response) . PHP_EOL;
}
