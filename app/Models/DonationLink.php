<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DonationLink extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'application_id',
        'code',
        'status',
        'expires_at',
        'first_accessed_at',
        'last_accessed_at',
        'access_count',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'first_accessed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Boot method to automatically generate unique code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donationLink) {
            if (empty($donationLink->code)) {
                $donationLink->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique donation code
     */
    public static function generateUniqueCode($length = 32)
    {
        do {
            $code = Str::random($length);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the application that owns the donation link
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who created the link
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all contributions for this donation link
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Get successful contributions only
     */
    public function successfulContributions()
    {
        return $this->hasMany(Contribution::class)->where('payment_status', Contribution::STATUS_COMPLETED);
    }


    /**
     * Get the full donation URL
     */
    public function getFullUrlAttribute()
    {
        return url('/donate/' . $this->code);
    }

    /**
     * Check if the link is active
     */
    public function isActive()
    {
        return $this->status === 'active' &&
            ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if the link has expired
     */
    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Record link access
     */
    public function recordAccess()
    {
        $this->increment('access_count');

        if ($this->first_accessed_at === null) {
            $this->first_accessed_at = now();
        }

        $this->last_accessed_at = now();
        $this->save();
    }

    /**
     * Deactivate the link
     */
    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Activate the link
     */
    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Get total amount raised through this link
     */
    public function getTotalRaisedAttribute(): float
    {
        return $this->successfulContributions()->sum('amount');
    }

    /**
     * Get total number of successful donations
     */
    public function getTotalDonationsAttribute(): int
    {
        return $this->successfulContributions()->count();
    }

    /**
     * Scope for active donation links
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
