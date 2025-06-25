<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationUser extends Pivot
{
    protected $table = 'application_user';

    /**
     * The application associated with the pivot.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * The role associated with the pivot.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The user associated with the pivot.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
