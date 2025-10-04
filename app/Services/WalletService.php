<?php

namespace App\Services;

use App\Enums\TransactionName;
use App\Models\User;

class WalletService
{
    protected CustomWalletService $customWalletService;

    public function __construct(CustomWalletService $customWalletService)
    {
        $this->customWalletService = $customWalletService;
    }

    public function forceTransfer(User $from, User $to, float $amount, TransactionName $transaction_name, array $meta = [])
    {
        return $this->customWalletService->forceTransfer($from, $to, $amount, $transaction_name, $meta);
    }

    public function transfer(User $from, User $to, float $amount, TransactionName $transaction_name, array $meta = [])
    {
        return $this->customWalletService->transfer($from, $to, $amount, $transaction_name, $meta);
    }

    public function deposit(User $user, float $amount, TransactionName $transaction_name, array $meta = [])
    {
        return $this->customWalletService->deposit($user, $amount, $transaction_name, $meta);
    }

    public function withdraw(User $user, float $amount, TransactionName $transaction_name, array $meta = [])
    {
        return $this->customWalletService->withdraw($user, $amount, $transaction_name, $meta);
    }

    public static function buildTransferMeta(User $user, User $target_user, TransactionName $transaction_name, array $meta = [])
    {
        return array_merge([
            'name' => $transaction_name,
            'opening_balance' => $user->balance,
            'target_user_id' => $target_user->id,
        ], $meta);
    }

    public static function buildDepositMeta(User $user, User $target_user, TransactionName $transaction_name, array $meta = [])
    {
        return array_merge([
            'name' => $transaction_name->value,
            'opening_balance' => $user->balance,
            'target_user_id' => $target_user->id,
        ], $meta);
    }
}
