<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportDocument extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable fields
     * Indexed: 'contribution_reason_id', 'status'
     */
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'contribution_reason_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_hash',
        'status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Polymorphic relationship
    public function documentable()
    {
        return $this->morphTo();
    }

    public function contributionReason()
    {
        return $this->belongsTo(ContributionReason::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
