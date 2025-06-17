<?php

namespace App\Enums;

enum UserType: string
{
    case Individual = 'individual';
    case Company = 'company';

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Use cases:
     *  1. Form Validation
     *      'status' => ['required', Rule::in(UserStatus::values())],
            'user_type' => ['required', Rule::in(UserType::values())],
        2. Seeder/Factory
            $user->status = UserStatus::Active->value;
        3. Blade
            @if ($user->user_type === \App\Enums\UserType::Individual->value)
                <p>This user is an individual.</p>
            @endif
     */
}
