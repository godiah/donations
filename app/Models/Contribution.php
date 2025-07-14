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

    /**
     * Calculate platform fee for any payment method and currency
     */
    public function calculatePlatformFee(?float $feePercentage = null, ?Transaction $transaction = null): void
    {
        $feePercentage = $feePercentage ?? self::DEFAULT_PLATFORM_FEE_PERCENTAGE;

        // Get the transaction amount based on payment method
        $amount = $this->getTransactionAmount($transaction);

        // Validate that amount is greater than 0
        if ($amount <= 0) {
            $this->platform_fee_percentage = $feePercentage;
            $this->platform_fee = 0.00;
            $this->net_amount = $amount;
            return;
        }

        // Calculate platform fee in the original currency
        $calculatedFee = ($amount * $feePercentage) / 100;
        $roundedFee = round($calculatedFee, 2);

        $this->platform_fee_percentage = $feePercentage;
        $this->platform_fee = $roundedFee;
        $this->net_amount = $amount - $roundedFee;

        Log::info('Platform fee calculated', [
            'contribution_id' => $this->id,
            'payment_method' => $this->payment_method,
            'currency' => $this->currency,
            'original_amount' => $amount,
            'platform_fee' => $this->platform_fee,
            'net_amount' => $this->net_amount,
            'fee_percentage' => $feePercentage
        ]);
    }

    /**
     * Get transaction amount based on payment method
     */
    public function getTransactionAmount(?Transaction $transaction = null): float
    {
        // If a specific transaction is provided, use its amount
        if ($transaction) {
            if ($transaction->gateway === Transaction::GATEWAY_MPESA && $transaction->mpesa_amount) {
                return (float) $transaction->mpesa_amount;
            }
            return (float) $transaction->amount;
        }

        // For M-Pesa payments, get the actual transaction amount
        if ($this->payment_method === 'mpesa') {
            $mpesaTransaction = $this->transactions()
                ->where('gateway', Transaction::GATEWAY_MPESA)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->first();

            if ($mpesaTransaction && $mpesaTransaction->mpesa_amount) {
                return (float) $mpesaTransaction->mpesa_amount;
            }
        }

        // For CyberSource payments or fallback, use contribution amount
        return (float) $this->amount;
    }

    /**
     * Get net amount
     */
    public function getNetAmount(): float
    {
        if (!is_null($this->net_amount)) {
            return $this->net_amount;
        }

        // Get transaction amount based on payment method
        $amount = $this->getTransactionAmount();

        return $amount - ($this->platform_fee ?? 0);
    }

    /**
     * Get net amount converted to KES for wallet crediting
     */
    public function getNetAmountInKes(): float
    {
        $netAmount = $this->getNetAmount();

        // If already in KES, return as is
        if ($this->currency === 'KES') {
            return $netAmount;
        }

        // Convert to KES using currency service
        $currencyService = app(\App\Services\CurrencyService::class);
        $convertedAmount = $currencyService->convertCurrency($netAmount, $this->currency, 'KES');

        Log::info('Net amount converted to KES for wallet credit', [
            'contribution_id' => $this->id,
            'original_currency' => $this->currency,
            'original_net_amount' => $netAmount,
            'converted_amount_kes' => $convertedAmount
        ]);

        return $convertedAmount;
    }

    /**
     * Get platform fee converted to KES
     */
    public function getPlatformFeeInKes(): float
    {
        $platformFee = $this->platform_fee ?? 0;

        // If already in KES, return as is
        if ($this->currency === 'KES') {
            return $platformFee;
        }

        // Convert to KES using currency service
        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->convertCurrency($platformFee, $this->currency, 'KES');
    }

    /**
     * Get gross amount converted to KES
     */
    public function getGrossAmountInKes(): float
    {
        $grossAmount = $this->getTransactionAmount();

        // If already in KES, return as is
        if ($this->currency === 'KES') {
            return $grossAmount;
        }

        // Convert to KES using currency service
        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->convertCurrency($grossAmount, $this->currency, 'KES');
    }

    /**
     * Check if this is a CyberSource transaction
     */
    public function isCyberSourceTransaction(): bool
    {
        return $this->payment_method === 'card' && !empty($this->cybersource_transaction_id);
    }

    /**
     * Check if this is an M-Pesa transaction
     */
    public function isMpesaTransaction(): bool
    {
        return $this->payment_method === 'mpesa';
    }

    /**
     * Get currency display with symbol
     */
    public function getFormattedAmount(): string
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->formatAmount($this->getTransactionAmount(), $this->currency);
    }

    /**
     * Get net amount formatted with currency
     */
    public function getFormattedNetAmount(): string
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->formatAmount($this->getNetAmount(), $this->currency);
    }
}
