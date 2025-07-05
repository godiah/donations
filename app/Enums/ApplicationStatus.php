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
    case Resubmitted = 'resubmitted';

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
            self::Resubmitted => 'Resubmitted',
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
            self::Resubmitted => 'bg-purple-100 text-purple-800',
        };
    }

    /**
     * Get status color class for active filter buttons
     */
    public function getActiveColorClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-600 text-white',
            self::Submitted => 'bg-blue-600 text-white',
            self::UnderReview => 'bg-yellow-600 text-white',
            self::AdditionalInfoRequired => 'bg-orange-600 text-white',
            self::Approved => 'bg-green-600 text-white',
            self::Rejected => 'bg-red-600 text-white',
            self::Resubmitted => 'bg-purple-900 text-white',
        };
    }

    /**
     * Get SVG icon for status
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>',

            self::Submitted => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L18,9.5L16.59,8.09L11,13.67L7.91,10.59L6.5,12L11,16.5Z"/>
                                </svg>',

            self::UnderReview => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                     <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17A1.5,1.5 0 0,1 10.5,15.5A1.5,1.5 0 0,1 12,14A1.5,1.5 0 0,1 13.5,15.5A1.5,1.5 0 0,1 12,17M12,13A1.5,1.5 0 0,1 10.5,11.5V7.5A1.5,1.5 0 0,1 12,6A1.5,1.5 0 0,1 13.5,7.5V11.5A1.5,1.5 0 0,1 12,13Z"/>
                                 </svg>',

            self::AdditionalInfoRequired => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"/>
                                            </svg>',

            self::Approved => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                   <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L18,9.5L16.59,8.09L11,13.67L7.91,10.59L6.5,12L11,16.5Z"/>
                               </svg>',

            self::Rejected => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                   <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M15.4,9L14,7.6L12,9.6L10,7.6L8.6,9L10.6,11L8.6,13L10,14.4L12,12.4L14,14.4L15.4,13L13.4,11L15.4,9Z"/>
                               </svg>',

            self::Resubmitted => '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                      <path d="M12,6V9L16,5L12,1V4A8,8 0 0,0 4,12C4,13.57 4.46,15.03 5.24,16.26L6.7,14.8C6.25,13.97 6,13 6,12A6,6 0 0,1 12,6M18.76,7.74L17.3,9.2C17.74,10.04 18,11 18,12A6,6 0 0,1 12,18V15L8,19L12,23V20A8,8 0 0,0 20,12C20,10.43 19.54,8.97 18.76,7.74Z"/>
                                  </svg>',
        };
    }

    /**
     * Get all icon for "All" status
     */
    public static function getAllIcon(): string
    {
        return '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19,3H5C3.9,3 3,3.9 3,5V19C3,20.1 3.9,21 5,21H19C20.1,21 21,20.1 21,19V5C21,3.9 20.1,3 19,3M19,19H5V5H19V19M17,12H7V10H17V12M15,16H7V14H15V16M17,8H7V6H17V8Z"/>
                </svg>';
    }

    /**
     * Get filterable statuses for admin interface
     */
    public static function getFilterableStatuses(): array
    {
        return [
            'all' => 'All Applications',
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'additional_info_required' => 'Additional Info Required',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'resubmitted' => 'Resubmitted',
        ];
    }

    /**
     * Get status enum from string value
     */
    public static function fromValue(string $value): ?self
    {
        return match ($value) {
            'draft' => self::Draft,
            'submitted' => self::Submitted,
            'under_review' => self::UnderReview,
            'additional_info_required' => self::AdditionalInfoRequired,
            'approved' => self::Approved,
            'rejected' => self::Rejected,
            'resubmitted' => self::Resubmitted,
            default => null,
        };
    }
}
