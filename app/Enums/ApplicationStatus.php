<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case AdditionalInfoRequired = 'additional_info_required';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display name for status
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::UnderReview => 'Under Review',
            self::AdditionalInfoRequired => 'Additional Info Required',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Get status color class for UI
     */
    public function getColorClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-800',
            self::Submitted => 'bg-blue-100 text-blue-800',
            self::UnderReview => 'bg-yellow-100 text-yellow-800',
            self::AdditionalInfoRequired => 'bg-orange-100 text-orange-800',
            self::Approved => 'bg-green-100 text-green-800',
            self::Rejected => 'bg-red-100 text-red-800',
            self::Cancelled => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get filterable statuses for admin interface
     */
    public static function getFilterableStatuses(): array
    {
        return [
            'all' => 'All',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'additional_info' => 'Additional Info',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }
}
