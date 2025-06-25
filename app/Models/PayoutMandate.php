<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutMandate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'type',
        'maker_id',
        'checker_id',
        'checker_name',
        'checker_email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function isDual()
    {
        return $this->type === 'dual';
    }

    public function isSingle()
    {
        return $this->type === 'single';
    }
}
