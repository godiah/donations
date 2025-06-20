<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * User who created the application
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
}
