<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     * Indexed: type_key, is_active
     */
    protected $fillable = [
        'type_key',
        'display_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get contribution reasons that require this document type
     */
    public function contributionReasons()
    {
        return ContributionReason::where('requires_document', true)
            ->whereJsonContains('required_document_types', $this->type_key)
            ->get();
    }
}
