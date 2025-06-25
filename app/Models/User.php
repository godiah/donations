<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     * Indexed: email and status
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type', // individual or company
        'status', // active, inactive, suspended
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class,
            'status' => UserStatus::class,
        ];
    }

    /**
     * Roles that belong to this user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role, optionally scoped to an application.
     */
    public function hasRole(string $roleName, ?int $applicationId = null): bool
    {
        $role = Role::where('name', $roleName)->first();
        if (!$role) return false;

        if ($applicationId) {
            return $this->applications()
                ->wherePivot('role_id', $role->id)
                ->where('application_user.application_id', $applicationId)
                ->exists();
        }

        return $this->roles()->where('role_id', $role->id)->exists();
    }



    /**
     * Assign a role to the user, optionally scoped to an application.
     */
    public function assignRole(string $roleName, ?int $applicationId = null): bool
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return false;
        }

        $applicationSpecificRoles = ['payout_maker', 'payout_checker', 'single_mandate_user'];

        if (in_array($roleName, $applicationSpecificRoles)) {
            if (is_null($applicationId)) {
                throw new \InvalidArgumentException("Application ID is required for role: {$roleName}");
            }

            // Assign application-specific role
            $this->applications()->syncWithoutDetaching([
                $applicationId => ['role_id' => $role->id]
            ]);
        } else {
            if (!is_null($applicationId)) {
                throw new \InvalidArgumentException("Application ID is not applicable for role: {$roleName}");
            }

            // Assign global role
            $this->roles()->syncWithoutDetaching([$role->id]);
        }

        return true;
    }


    /**
     * Remove a role from the user, optionally scoped to an application.
     */
    public function removeRole(string $roleName, ?int $applicationId = null): bool
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return false;
        }

        if ($applicationId) {
            // Remove role from pivot for the application
            $this->applications()->updateExistingPivot($applicationId, ['role_id' => null]);

            // Optionally: detach user entirely if no other role is needed
            // $this->applications()->detach($applicationId);

            return true;
        }

        // Remove global role
        return $this->roles()->detach($role->id) > 0;
    }


    /**
     * Get name of roles
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('display_name')->toArray();
    }

    public function individuals()
    {
        return $this->hasMany(Individual::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    // Applications created by this user
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function makerMandates()
    {
        return $this->hasMany(PayoutMandate::class, 'maker_id');
    }

    public function checkerMandates()
    {
        return $this->hasMany(PayoutMandate::class, 'checker_id');
    }

    // Applications reviewed by this user
    public function reviewedApplications()
    {
        return $this->hasMany(Application::class, 'reviewed_by');
    }
}
