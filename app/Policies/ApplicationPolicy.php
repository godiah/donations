<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the application
     */
    public function view(User $user, Application $application)
    {
        // Admins can view all applications
        if ($user->hasRole('admin')) {
            return true;
        }

        // User is the original applicant
        if ($user->id === $application->user_id) {
            return true;
        }

        // User is the checker for this application
        if (
            $application->payoutMandate &&
            $application->payoutMandate->checker_id === $user->id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create payouts for this application
     */
    public function createPayout(User $user, Application $application)
    {
        // Single mandate - user can create and approve
        if (
            $application->payoutMandate &&
            $application->payoutMandate->isSingle() &&
            $user->id === $application->user_id
        ) {
            return true;
        }

        // Dual mandate - only maker can create
        if (
            $application->payoutMandate &&
            $application->payoutMandate->isDual() &&
            $user->id === $application->payoutMandate->maker_id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can approve payouts for this application
     */
    public function approvePayout(User $user, Application $application)
    {
        // Single mandate - user can approve their own payouts
        if (
            $application->payoutMandate &&
            $application->payoutMandate->isSingle() &&
            $user->id === $application->user_id
        ) {
            return true;
        }

        // Dual mandate - only checker can approve
        if (
            $application->payoutMandate &&
            $application->payoutMandate->isDual() &&
            $user->id === $application->payoutMandate->checker_id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the application.
     */
    public function update(User $user, Application $application): bool
    {
        // Only the owner can update their application
        if ($application->applicant->user_id !== $user->id) {
            return false;
        }
    
        // Only applications requiring additional information can be updated
        if ($application->status !== \App\Enums\ApplicationStatus::AdditionalInfoRequired) {
            return false;
        }
    
        return true;
    }

}
