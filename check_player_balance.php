<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('user_name', 'PLAYER0101')->first();
if ($user) {
    echo "User: " . $user->user_name . PHP_EOL;
    echo "Balance: " . $user->balance . PHP_EOL;
    echo "Balance Type: " . gettype($user->balance) . PHP_EOL;
    echo "Balance Float: " . $user->balanceFloat . PHP_EOL;
} else {
    echo "User not found" . PHP_EOL;
}
