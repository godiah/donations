<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
     * Check if user has a given role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Assign the given role to the user.
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching($role->id);
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
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // Applications reviewed by this user
    public function reviewedApplications()
    {
        return $this->hasMany(Application::class, 'reviewed_by');
    }
}
