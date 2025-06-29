<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KycVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'individual_id',
        'application_id',
        'job_id',
        'smile_job_id',
        'id_type',
        'id_number',
        'country_code',
        'status',
        'result_code',
        'result_text',
        'verification_data',
        'actions',
        'failure_reason',
        'submitted_at',
        'completed_at',
        'initiated_by',
    ];

    protected $casts = [
        'verification_data' => 'array',
        'actions' => 'array',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function individual()
    {
        return $this->belongsTo(Individual::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    /**
     * Scopes
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Helper methods
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get the verification result summary
     */
    public function getResultSummary(): array
    {
        if (!$this->actions) {
            return [];
        }

        return [
            'names_match' => $this->actions['Names'] ?? 'N/A',
            'id_verified' => $this->actions['Verify_ID_Number'] ?? 'N/A',
            'dob_match' => $this->actions['DOB'] ?? 'N/A',
            'gender_match' => $this->actions['Gender'] ?? 'N/A',
            'phone_match' => $this->actions['Phone_Number'] ?? 'N/A',
        ];
    }

    /**
     * Check if names match (exact or partial)
     */
    public function namesMatch(): bool
    {
        $namesResult = $this->actions['Names'] ?? '';
        return in_array($namesResult, ['Exact Match', 'Partial Match']);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'verified' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-yellow-100 text-yellow-800',
        };
    }

    /**
     * Get formatted status text
     */
    public function getStatusText(): string
    {
        return match ($this->status) {
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'processing' => 'Processing',
            'failed' => 'Failed',
            default => 'Pending',
        };
    }
}
