<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\DonationNotifiable;
use App\Notifications\DonationCompletedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DonationNotificationService
{
    /**
     * Send donation completion notifications
     */
    public function sendDonationNotifications(Contribution $contribution): void
    {
        try {
            // Verify contribution is completed
            if ($contribution->payment_status !== Contribution::STATUS_COMPLETED) {
                Log::warning('Attempted to send notifications for incomplete donation', [
                    'contribution_id' => $contribution->id,
                    'status' => $contribution->payment_status,
                ]);
                return;
            }

            Log::info('Sending donation notifications', [
                'contribution_id' => $contribution->id,
                'payment_method' => $contribution->payment_method,
                'email' => $contribution->email ? 'provided' : 'not_provided',
                'phone' => $contribution->phone ? 'provided' : 'not_provided',
                'amount' => $contribution->amount,
                'currency' => $contribution->currency,
            ]);

            // Create a proper notifiable instance that can be serialized
            $notifiable = \App\Models\DonationNotifiable::forContribution($contribution);

            // Send the notification (queued)
            $notifiable->notify(new DonationCompletedNotification($contribution));

            Log::info('Donation notifications queued successfully', [
                'contribution_id' => $contribution->id,
                'payment_method' => $contribution->payment_method,
            ]);
        } catch (\Exception $e) {
            // Don't let notification failures break the payment flow
            Log::error('Failed to send donation notifications', [
                'contribution_id' => $contribution->id,
                'payment_method' => $contribution->payment_method ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
