<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name'];

    /**
     * Users that belong to this role
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Users associated with this role for specific applications-specific roles).
     */
    public function applicationUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'application_user')
            ->withPivot('application_id')
            ->withTimestamps();
    }
}
