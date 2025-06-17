<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable fields
     * Indexed: user_id, contribution_reason_id; registration_certificate; target_date
     */
    protected $fillable = [
        'user_id',
        'contribution_name',
        'contribution_description',
        'contribution_reason_id',
        'company_name',
        'registration_certificate',
        'pin_number',
        'cr12',
        'cr12_date',
        'address',
        'city',
        'county',
        'postal_code',
        'country',
        'bank_id',
        'bank_account_number',
        'bank_account_proof',
        'settlement',
        'contact_persons',
        'target_amount',
        'target_date',
        'amount_raised',
        'fees_charged',
        'additional_info',
    ];

    protected $casts = [
        'cr12_date' => 'date',
        'target_date' => 'date',
        'amount_raised' => 'decimal:2',
        'fees_charged' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'contact_persons' => 'array',
        'additional_info' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributionReason()
    {
        return $this->belongsTo(ContributionReason::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    // Polymorphic relationship to applications
    public function applications()
    {
        return $this->morphMany(Application::class, 'applicant');
    }

    public function supportDocuments()
    {
        return $this->morphMany(SupportDocument::class, 'documentable');
    }

    public function getRequiredDocumentTypes()
    {
        return $this->contributionReason->getDocumentTypesWithDetails();
    }

    public function hasAllRequiredDocuments()
    {
        $requiredTypes = $this->contributionReason->required_document_types ?? [];
        $uploadedTypes = $this->supportDocuments()
            ->where('status', 'verified')
            ->get()
            ->pluck('document_type')
            ->unique()
            ->toArray();

        return empty(array_diff($requiredTypes, $uploadedTypes));
    }
}
