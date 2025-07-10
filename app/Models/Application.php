<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable fields
     * Indexed: status; submitted_at; applicant_type, applicant_id
     */
    protected $fillable = [
        'application_number',
        'user_id',
        'applicant_type',
        'applicant_id',
        'status',
        'admin_comments',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /**
     * Users associated with this application and their roles.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'application_user')
            ->withPivot('role_id')
            ->withTimestamps()
            ->using(ApplicationUser::class);
    }

    /**
     * For the application owner (uses user_id field)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relationship to Individual or Company
     */
    public function applicant()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        $payoutMakerRoleId = Role::where('name', 'payout_maker')->value('id');
        return $this->users()->wherePivot('role_id', $payoutMakerRoleId)->first();
    }

    // User who reviewed the application
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documents()
    {
        return $this->morphMany(SupportDocument::class, 'documentable');
    }

    // Get all documents for this application through the applicant
    public function getDocuments()
    {
        return $this->applicant->supportDocuments();
    }

    public function payoutMandate()
    {
        return $this->hasOne(PayoutMandate::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get donation links for this application
     */
    public function donationLinks()
    {
        return $this->hasMany(DonationLink::class);
    }

    /**
     * Get active donation links
     */
    public function activeDonationLinks()
    {
        return $this->hasMany(DonationLink::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Get fee structure display
     */
    public function getFeeStructureAttribute()
    {
        // You can customize this based on your fee structure logic
        return [
            'type' => 'percentage', // or 'fixed' or 'range'
            'value' => '5%', // or specific amount/range
            'description' => '5% of total contribution'
        ];
    }

    public function hasDualMandate()
    {
        return $this->payoutMandate && $this->payoutMandate->isDual();
    }

    public function getMaker()
    {
        return $this->payoutMandate ? $this->payoutMandate->maker : $this->user;
    }

    public function getChecker()
    {
        return $this->payoutMandate ? $this->payoutMandate->checker : null;
    }

     /**
     * Check if application has any rejected support documents
     */
    public function hasRejectedDocuments(): bool
    {
        return $this->applicant->supportDocuments()
            ->where('status', 'rejected')
            ->exists();
    }

    /**
     * Check if individual application has unverified KYC
     */
    public function hasUnverifiedKyc(): bool
    {
        if ($this->applicant_type !== 'App\\Models\\Individual') {
            return false;
        }

        // Check if there's any verified KYC for this application
        return !$this->applicant
            ->kycVerifications()
            ->where('application_id', $this->id)
            ->where('status', 'verified')
            ->exists();
    }

    /**
     * Get KYC status for individual applications
     */
    public function getKycStatus(): string
    {
        if ($this->applicant_type !== 'App\\Models\\Individual') {
            return 'not_applicable';
        }

        $hasVerified = $this->applicant
            ->kycVerifications()
            ->where('application_id', $this->id)
            ->where('status', 'verified')
            ->exists();

        if ($hasVerified) {
            return 'verified';
        }

        $latestKyc = $this->applicant
            ->kycVerifications()
            ->where('application_id', $this->id)
            ->latest()
            ->first();

        return $latestKyc ? $latestKyc->status : 'pending';
    }

    /**
     * Determine if admin form should be shown
     */
    public function shouldShowAdminForm(): bool
    {
        // Rule 1: Any rejected support documents
        if ($this->hasRejectedDocuments()) {
            return true;
        }

        // Rule 2: Individual without verified KYC
        if ($this->hasUnverifiedKyc()) {
            return true;
        }

        // Rule 3: Company application under review
        if ($this->applicant_type === 'App\\Models\\Company' && 
            $this->status->value === 'under_review') {
            return true;
        }

        return false;
    }
}
