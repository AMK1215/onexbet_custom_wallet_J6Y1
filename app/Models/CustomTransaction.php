<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_user_id',
        'amount',
        'type',
        'transaction_name',
        'old_balance',
        'new_balance',
        'meta',
        'uuid',
        'confirmed',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'old_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
        'meta' => 'json',
        'confirmed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who performed the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the target user of the transaction
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Get amount as float
     */
    public function getAmountFloatAttribute(): float
    {
        return (float) $this->amount;
    }

    /**
     * Get old balance as float
     */
    public function getOldBalanceFloatAttribute(): float
    {
        return (float) $this->old_balance;
    }

    /**
     * Get new balance as float
     */
    public function getNewBalanceFloatAttribute(): float
    {
        return (float) $this->new_balance;
    }

    /**
     * Scope for confirmed transactions
     */
    public function scopeConfirmed($query)
    {
        return $query->where('confirmed', true);
    }

    /**
     * Scope for deposits
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope for withdrawals
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdraw');
    }

    /**
     * Scope for transfers
     */
    public function scopeTransfers($query)
    {
        return $query->where('type', 'transfer');
    }
}
