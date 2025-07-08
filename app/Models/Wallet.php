<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'status',
        'total_received',
        'total_withdrawn',
        'pending_withdrawals',
        'last_activity_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_received' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_withdrawals' => 'decimal:2',
        'last_activity_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    // Currency constants
    const CURRENCY_KES = 'KES';
    const CURRENCY_USD = 'USD';

    /**
     * Get the user that owns the wallet
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this wallet
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get all withdrawal requests for this wallet
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Get credit transactions
     */
    public function credits()
    {
        return $this->transactions()->where('type', WalletTransaction::TYPE_CREDIT);
    }

    /**
     * Get debit transactions
     */
    public function debits()
    {
        return $this->transactions()->where('type', WalletTransaction::TYPE_DEBIT);
    }

    /**
     * Get pending transactions
     */
    public function pendingTransactions()
    {
        return $this->transactions()->where('status', WalletTransaction::STATUS_PENDING);
    }

    /**
     * Check if wallet is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if wallet has sufficient balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Get available balance (balance - pending withdrawals)
     */
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->pending_withdrawals;
    }

    /**
     * Scope for active wallets
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for specific currency
     */
    public function scopeForCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }
}
