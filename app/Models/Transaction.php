<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'contribution_id',
        'transaction_id',
        'gateway_transaction_id',
        'cybersource_transaction_uuid',
        'cybersource_reference_number',
        'cybersource_auth_code',
        'cybersource_decision',
        'cybersource_reason_code',
        'cybersource_payment_token',
        'gateway',
        'type',
        'status',
        'amount',
        'currency',
        'gateway_response',
        'processed_at',
        'notes',
        'mpesa_checkout_request_id',
        'mpesa_merchant_request_id',
        'mpesa_receipt_number',
        'mpesa_amount',
        'mpesa_phone_number',
        'mpesa_transaction_date',
        'mpesa_payment_type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
        'mpesa_amount' => 'decimal:2',
        'mpesa_transaction_date' => 'datetime',
    ];

    // Transaction types
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';
    const TYPE_PARTIAL_REFUND = 'partial_refund';

    // Transaction statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DECLINED = 'declined';
    const STATUS_REVIEW = 'review';

    // Gateway types
    const GATEWAY_CYBERSOURCE = 'cybersource';
    const GATEWAY_MPESA = 'mpesa';

    /**
     * Get the contribution that owns the transaction
     */
    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }

    // Add method to check if transaction is M-Pesa
    public function isMpesaTransaction(): bool
    {
        return $this->gateway === self::GATEWAY_MPESA;
    }

    // Add method to get M-Pesa receipt details
    public function getMpesaReceiptDetails(): ?array
    {
        if (!$this->isMpesaTransaction()) {
            return null;
        }

        return [
            'receipt_number' => $this->mpesa_receipt_number,
            'phone_number' => $this->mpesa_phone_number,
            'amount' => $this->mpesa_amount,
            'transaction_date' => $this->mpesa_transaction_date,
            'payment_type' => $this->mpesa_payment_type,
        ];
    }

    /**
     * Check if transaction is from CyberSource
     */
    public function isCyberSourceTransaction(): bool
    {
        return $this->gateway === self::GATEWAY_CYBERSOURCE;
    }

    /**
     * Get transaction display status
     */
    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_DECLINED => 'Declined',
            self::STATUS_REVIEW => 'Under Review',
            default => 'Unknown',
        };
    }

    /**
     * Scope for successful transactions
     */
    public function scopeSuccessful($query)
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
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_FAILED, self::STATUS_DECLINED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if transaction failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_DECLINED, self::STATUS_CANCELLED]);
    }
}
