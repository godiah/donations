<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'account_number',
        'account_name',
        'bank_id',
        'is_primary',
        'is_verified',
        'additional_info',
        'paybill_number',
        'paybill_account_name',
        'paybill_settings'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the payout method
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot method to handle primary payout method logic
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payoutMethod) {
            if ($payoutMethod->is_primary) {
                // Make other methods non-primary for this user
                static::where('user_id', $payoutMethod->user_id)
                    ->update(['is_primary' => false]);
            }
        });

        static::updating(function ($payoutMethod) {
            if ($payoutMethod->is_primary && $payoutMethod->isDirty('is_primary')) {
                // Make other methods non-primary for this user
                static::where('user_id', $payoutMethod->user_id)
                    ->where('id', '!=', $payoutMethod->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function isPaybillMethod(): bool
    {
        return $this->type === 'paybill';
    }

    public function getPaybillDetails(): ?array
    {
        if (!$this->isPaybillMethod()) {
            return null;
        }

        return [
            'paybill_number' => $this->paybill_number,
            'account_name' => $this->paybill_account_name,
            'account_number' => $this->account_number,
            'settings' => $this->paybill_settings,
        ];
    }

    public function validatePaybillAccount(string $accountNumber, string $accountName): bool
    {
        if (!$this->isPaybillMethod()) {
            return false;
        }

        return $this->account_number === $accountNumber &&
            strcasecmp($this->paybill_account_name, $accountName) === 0;
    }

    /**
    * Validate paybill transaction
    */
    public function validatePaybillTransaction(string $paybillNumber, string $accountNumber, string $accountName): bool
    {
        if (!$this->isPaybillMethod()) {
            return false;
        }

        return $this->paybill_number === $paybillNumber &&
            $this->account_number === $accountNumber &&
            strcasecmp($this->paybill_account_name, $accountName) === 0;
    }

    /**
     * Get formatted account display
     */
    public function getFormattedAccountAttribute(): string
    {
        if ($this->type === 'mobile_money') {
            return $this->provider . ' - ' . $this->account_number;
        } elseif ($this->type === 'bank_account') {
            return $this->bank ? ($this->bank->display_name . ' - ' . $this->account_number) : 'Bank Account - ' . $this->account_number;
        } elseif ($this->type === 'paybill') {
            return 'Paybill ' . $this->paybill_number . ' - ' . $this->account_number . ' (' . $this->provider . ')';
        }

        return $this->account_number;
    }

    /**
     * Get paybill settings as array
     */
    public function getPaybillSettingsAttribute($value): ?array
    {
        if (!$value) {
            return null;
        }

        return json_decode($value, true);
    }

    /**
     * Set paybill settings as JSON
     */
    public function setPaybillSettingsAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['paybill_settings'] = json_encode($value);
        } else {
            $this->attributes['paybill_settings'] = $value;
        }
    }

    /**
     * Get paybill description from settings
     */
    public function getPaybillDescriptionAttribute(): ?string
    {
        if (!$this->isPaybillMethod()) {
            return null;
        }

        $settings = $this->paybill_settings;
        return $settings['description'] ?? null;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        if (!$this->is_verified) {
            return 'bg-yellow-100 text-yellow-800';
        }

        if ($this->is_primary) {
            return 'bg-green-100 text-green-800';
        }

        return 'bg-gray-100 text-gray-800';
    }

    /**
     * Get type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'mobile_money' => 'Mobile Money',
            'bank_account' => 'Bank Account',
            'paybill' => 'Paybill',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
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

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
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

    /**
     * Scope for paybill methods
     */
    public function scopePaybill($query)
    {
        return $query->where('type', 'paybill');
    }

    /**
     * Scope for mobile money methods
     */
    public function scopeMobileMoney($query)
    {
        return $query->where('type', 'mobile_money');
    }

    /**
     * Scope for bank account methods
     */
    public function scopeBankAccount($query)
    {
        return $query->where('type', 'bank_account');
    }
}
