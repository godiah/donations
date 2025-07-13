<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_link_id',
        'amount',
        'platform_fee',
        'net_amount',
        'platform_fee_percentage',
        'currency',
        'email',
        'phone',
        'donation_type',
        'payment_method',
        'payment_status',
        'cybersource_request_id',
        'cybersource_transaction_id',
        'cybersource_transaction_uuid',
        'cybersource_reference_number',
        'cybersource_auth_code',
        'cybersource_decision',
        'cybersource_reason_code',
        'cybersource_signed_field_names',
        'cybersource_signature',
        'cybersource_signed_date_time',
        'payment_response',
        'processed_at',
        'wallet_transaction_id',
        'wallet_credited',
        'wallet_credited_at',
        // Billing information
        'bill_to_forename',
        'bill_to_surname',
        'bill_to_address_line1',
        'bill_to_address_city',
        'bill_to_address_state',
        'bill_to_address_postal_code',
        'bill_to_address_country',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'platform_fee_percentage' => 'decimal:2',
        'payment_response' => 'array',
        'processed_at' => 'datetime',
        'wallet_credited_at' => 'datetime',
        'wallet_credited' => 'boolean',
        'cybersource_signed_date_time' => 'datetime',
    ];

    // Payment status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment method constants
    const METHOD_MPESA = 'mpesa';
    const METHOD_CARD = 'card';

    // Currency constants
    const CURRENCY_KES = 'KES';
    const CURRENCY_USD = 'USD';

    const DEFAULT_PLATFORM_FEE_PERCENTAGE = 5.00;

    /**
     * Get the donation link that owns the contribution
     */
    public function donationLink()
    {
        return $this->belongsTo(DonationLink::class);
    }

    /**
     * Get the transactions for this contribution
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    /**
     * Calculate and set platform fee for this contribution
     */
    public function calculatePlatformFee(?float $feePercentage = null, ?Transaction $transaction = null): void
    {
        $feePercentage = $feePercentage ?? self::DEFAULT_PLATFORM_FEE_PERCENTAGE;

        // If a transaction is provided, use its mpesa_amount
        if ($transaction && $transaction->mpesa_amount) {
            $amount = (float) $transaction->mpesa_amount;
        } else {
            // Get the actual M-Pesa transaction amount from the completed transaction
            $mpesaTransaction = $this->transactions()
                ->where('gateway', Transaction::GATEWAY_MPESA)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->first();

            // Use M-Pesa amount if available, otherwise fall back to contribution amount
            $amount = $mpesaTransaction && $mpesaTransaction->mpesa_amount
                ? (float) $mpesaTransaction->mpesa_amount
                : (float) $this->amount;
        }

        // Validate that amount is greater than 0
        if ($amount <= 0) {
            $this->platform_fee_percentage = $feePercentage;
            $this->platform_fee = 0.00;
            $this->net_amount = $amount;
            return;
        }

        // Calculate platform fee
        $calculatedFee = ($amount * $feePercentage) / 100;
        $roundedFee = round($calculatedFee, 2);

        $this->platform_fee_percentage = $feePercentage;
        $this->platform_fee = $roundedFee;
        $this->net_amount = $amount - $roundedFee;
    }

    /**
     * Get the net amount after platform fee
     */
    public function getNetAmount(): float
    {
        if (!is_null($this->net_amount)) {
            return $this->net_amount;
        }

        // Calculate net amount using M-Pesa transaction amount if available
        $mpesaTransaction = $this->transactions()
            ->where('gateway', Transaction::GATEWAY_MPESA)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->first();

        $amount = $mpesaTransaction && $mpesaTransaction->mpesa_amount
            ? (float) $mpesaTransaction->mpesa_amount
            : (float) $this->amount;

        return $amount - ($this->platform_fee ?? 0);
    }

    /**
     * Check if platform fee has been calculated
     */
    public function hasPlatformFeeCalculated(): bool
    {
        return !is_null($this->platform_fee) && $this->platform_fee >= 0;
    }

    /**
     * Scope for successful contributions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for pending contributions
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }

    /**
     * Check if contribution is completed
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === self::STATUS_COMPLETED;
    }

    /**
     * Check if contribution is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === self::STATUS_PENDING;
    }

    /**
     * Check if contribution failed
     */
    public function isFailed(): bool
    {
        return $this->payment_status === self::STATUS_FAILED;
    }

    /**
     * Check if contribution is from CyberSource
     */
    public function isCyberSourcePayment(): bool
    {
        return $this->payment_method === self::METHOD_CARD && !empty($this->cybersource_transaction_uuid);
    }

    /**
     * Get display name for billing
     */
    public function getBillingNameAttribute(): string
    {
        return trim($this->bill_to_forename . ' ' . $this->bill_to_surname);
    }

    /**
     * Get full billing address
     */
    public function getBillingAddressAttribute(): string
    {
        $address = array_filter([
            $this->bill_to_address_line1,
            $this->bill_to_address_city,
            $this->bill_to_address_state,
            $this->bill_to_address_postal_code,
            $this->bill_to_address_country,
        ]);

        return implode(', ', $address);
    }
}
