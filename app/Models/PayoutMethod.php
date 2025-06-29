<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'type',
        'provider',
        'account_number',
        'account_name',
        'bank_id',
        'is_primary',
        'is_verified',
        'additional_info',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the owning payable model (Individual or Company).
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Boot method to handle primary payout method logic
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payoutMethod) {
            if ($payoutMethod->is_primary) {
                // Make other methods non-primary for this payable
                static::where('payable_type', $payoutMethod->payable_type)
                    ->where('payable_id', $payoutMethod->payable_id)
                    ->update(['is_primary' => false]);
            }
        });

        static::updating(function ($payoutMethod) {
            if ($payoutMethod->is_primary && $payoutMethod->isDirty('is_primary')) {
                // Make other methods non-primary for this payable
                static::where('payable_type', $payoutMethod->payable_type)
                    ->where('payable_id', $payoutMethod->payable_id)
                    ->where('id', '!=', $payoutMethod->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get formatted account display
     */
    public function getFormattedAccountAttribute(): string
    {
        if ($this->type === 'mobile_money') {
            return $this->provider . ' - ' . $this->account_number;
        }

        return $this->bank_name . ' - ' . $this->account_number;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        if (!$this->is_verified) {
            return 'bg-yellow-100 text-yellow-800';
        }

        return $this->is_primary ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        if (!$this->is_verified) {
            return 'Unverified';
        }

        return $this->is_primary ? 'Primary' : 'Secondary';
    }

    /**
     * Scope for primary methods
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for verified methods
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
