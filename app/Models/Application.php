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
}
