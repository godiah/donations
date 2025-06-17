<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionReason extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'description',
        'requires_document',
        'required_document_types',
        'is_active',
    ];

    protected $casts = [
        'required_document_types' => 'array',
        'requires_document' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function individuals()
    {
        return $this->hasMany(Individual::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the document types required for this contribution reason
     */
    public function getDocumentTypesWithDetails()
    {
        if (!$this->requires_document || empty($this->required_document_types)) {
            return collect();
        }

        return DocumentType::whereIn('type_key', $this->required_document_types)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Check if a specific document type is required
     */
    public function requiresDocumentType(string $typeKey): bool
    {
        return $this->requires_document &&
            in_array($typeKey, $this->required_document_types ?? []);
    }
}
