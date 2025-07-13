<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'payout_method_id',
        'request_reference',
        'amount',
        'fee_amount',
        'net_amount',
        'withdrawal_method',
        'withdrawal_details',
        'status',
        'gateway_reference',
        'rejection_reason',
        'gateway_response',
        'approved_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'withdrawal_details' => 'array',
        'gateway_response' => 'array',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Withdrawal methods
    const METHOD_MPESA = 'mpesa';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_PAYBILL = 'paybill';

    // Method mapping from payout types to withdrawal methods
    const PAYOUT_TO_WITHDRAWAL_METHOD_MAP = [
        'mobile_money' => self::METHOD_MPESA,
        'bank_account' => self::METHOD_BANK_TRANSFER,
        'paybill' => self::METHOD_PAYBILL,
    ];

    /**
     * Get the user that owns this withdrawal request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet this withdrawal is from
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function payoutMethod()
    {
        return $this->belongsTo(PayoutMethod::class);
    }

    /**
     * Get withdrawal method from payout method type
     */
    public static function getWithdrawalMethodFromPayoutType(string $payoutType): string
    {
        return self::PAYOUT_TO_WITHDRAWAL_METHOD_MAP[$payoutType] ?? $payoutType;
    }

    public static function getAvailableWithdrawalMethods(): array
    {
        return [
            self::METHOD_MPESA,
            self::METHOD_BANK_TRANSFER,
            self::METHOD_PAYBILL,
        ];
    }

    /**
     * Create withdrawal details from payout method
     */
    public static function createWithdrawalDetailsFromPayoutMethod(PayoutMethod $payoutMethod): array
    {
        switch ($payoutMethod->type) {
            case 'mobile_money':
                return [
                    'method' => self::METHOD_MPESA,
                    'phone_number' => $payoutMethod->account_number,
                    'provider' => $payoutMethod->provider,
                ];

            case 'bank_account':
                return [
                    'method' => self::METHOD_BANK_TRANSFER,
                    'bank_name' => $payoutMethod->bank->name ?? $payoutMethod->provider,
                    'bank_code' => $payoutMethod->bank->code ?? null,
                    'account_number' => $payoutMethod->account_number,
                    'account_name' => $payoutMethod->account_name,
                ];

            case 'paybill':
                return [
                    'method' => self::METHOD_PAYBILL,
                    'paybill_number' => $payoutMethod->paybill_number,
                    'account_number' => $payoutMethod->account_number,
                    'account_name' => $payoutMethod->paybill_account_name,
                    'provider' => $payoutMethod->provider,
                ];

            default:
                throw new \InvalidArgumentException("Unsupported payout method type: {$payoutMethod->type}");
        }
    }

    /**
     * Check if withdrawal is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if withdrawal is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if withdrawal is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if withdrawal can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Check if withdrawal can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Scope for pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved withdrawals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for completed withdrawals
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Get formatted status attribute
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'secondary',
            self::STATUS_APPROVED => 'primary',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'neutral',
            default => 'neutral'
        };
    }
}
