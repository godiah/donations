<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'payout_mandate_id',
        'email',
        'name',
        'token',
        'expires_at',
        'accepted',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'accepted' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function payoutMandate()
    {
        return $this->belongsTo(PayoutMandate::class);
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isValid()
    {
        return !$this->accepted && !$this->isExpired();
    }

    public function scopeValid($query)
    {
        return $query->where('accepted', false)
            ->where('expires_at', '>', now());
    }
}
