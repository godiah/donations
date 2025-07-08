<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WithdrawalRequest;

class WithdrawalRequestPolicy
{
    public function view(User $user, WithdrawalRequest $withdrawal): bool
    {
        return $user->id === $withdrawal->user_id;
    }

    public function cancel(User $user, WithdrawalRequest $withdrawal): bool
    {
        return $user->id === $withdrawal->user_id && $withdrawal->canBeCancelled();
    }
}
