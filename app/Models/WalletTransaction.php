<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'transaction_reference',
        'type',
        'amount',
        'running_balance',
        'status',
        'source_type',
        'source_id',
        'gateway',
        'gateway_reference',
        'description',
        'metadata',
        'fee_amount',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    // Transaction types
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    // Transaction statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Source types
    const SOURCE_TYPE_DONATION = 'donation';
    const SOURCE_TYPE_WITHDRAWAL = 'withdrawal';
    const SOURCE_TYPE_ADJUSTMENT = 'adjustment';

    // Gateway types
    const GATEWAY_CYBERSOURCE = 'cybersource';
    const GATEWAY_MPESA = 'mpesa';
    const GATEWAY_BANK_TRANSFER = 'bank_transfer';

    /**
     * Get the wallet that owns this transaction
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the contribution if this is a donation credit
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Get the source model (polymorphic relationship)
     */
    public function source()
    {
        return $this->morphTo('source', 'source_type', 'source_id');
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is a credit
     */
    public function isCredit(): bool
    {
        return $this->type === self::TYPE_CREDIT;
    }

    /**
     * Check if transaction is a debit
     */
    public function isDebit(): bool
    {
        return $this->type === self::TYPE_DEBIT;
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for credit transactions
     */
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    /**
     * Scope for debit transactions
     */
    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }
}
