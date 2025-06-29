<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Individual extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    /**
     * Mass assignable fields
     * Indexed: user_id, contribution_reason_id ; target_date
     */
    protected $fillable = [
        'user_id',
        'contribution_name',
        'contribution_description',
        'contribution_reason_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'id_type_id',
        'id_number',
        'kyc_verified',
        'kyc_verified_at',
        'kyc_response_data',
        'kra_pin',
        'target_amount',
        'target_date',
        'amount_raised',
        'fees_charged',
        'additional_info',
    ];

    protected $casts = [
        'kyc_verified' => 'boolean',
        'kyc_verified_at' => 'datetime',
        'kyc_response_data' => 'array',
        'target_date' => 'date',
        'target_amount' => 'decimal:2',
        'amount_raised' => 'decimal:2',
        'fees_charged' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributionReason()
    {
        return $this->belongsTo(ContributionReason::class);
    }

    public function idType()
    {
        return $this->belongsTo(IdType::class);
    }

    // Polymorphic relationship to applications
    public function applications()
    {
        return $this->morphMany(Application::class, 'applicant');
    }

    // Polymorphic relationship to documents
    public function supportDocuments()
    {
        return $this->morphMany(SupportDocument::class, 'documentable');
    }

    // KYC verifications relationship
    public function kycVerifications()
    {
        return $this->hasMany(KycVerification::class);
    }

    /**
     * Get the latest KYC verification for a specific application
     */
    public function getLatestKycVerification(Application $application): ?KycVerification
    {
        return $this->kycVerifications()
            ->where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Check if individual is KYC verified for a specific application
     */
    public function isKycVerified(Application $application): bool
    {
        $verification = $this->getLatestKycVerification($application);
        return $verification && $verification->isVerified();
    }

    /**
     * Get KYC verification status for a specific application
     */
    public function getKycStatus(Application $application): string
    {
        $verification = $this->getLatestKycVerification($application);
        return $verification ? $verification->status : 'not_verified';
    }

    /**
     * Get the full name attribute by combining name fields
     */
    public function getFullNameAttribute(): string
    {
        $names = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name
        ]);

        return implode(' ', $names);
    }

    // Helper: Get required document types for this individual's contribution reason
    public function getRequiredDocumentTypes()
    {
        return $this->contributionReason->getDocumentTypesWithDetails();
    }

    // Helper: Check if all required documents are uploaded and verified
    public function hasAllRequiredDocuments()
    {
        $requiredTypes = $this->contributionReason->required_document_types ?? [];
        $uploadedTypes = $this->supportDocuments()
            ->where('status', 'verified')
            ->get()
            ->pluck('document_type') // You'd need to add this field or derive it
            ->unique()
            ->toArray();

        return empty(array_diff($requiredTypes, $uploadedTypes));
    }
}
