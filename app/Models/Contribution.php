<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_link_id',
        'amount',
        'currency',
        'email',
        'phone',
        'donation_type',
        'payment_method',
        'payment_status',
        'cybersource_request_id',
        'cybersource_transaction_id',
        'payment_response',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_response' => 'array',
        'processed_at' => 'datetime',
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
}
