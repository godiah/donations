<?php

namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class DonationCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contribution;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contribution $contribution)
    {
        $this->contribution = $contribution;
    }

    /**
     * Get the contribution instance.
     */
    public function getContribution()
    {
        return $this->contribution;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = [];

        // Always include email if available
        if ($this->contribution->email) {
            $channels[] = 'mail';
        }

        // Include SMS and WhatsApp if phone number is available
        if ($this->contribution->phone) {
            $channels[] = 'sms';
            $channels[] = 'whatsapp';
        }

        Log::info('Donation notification channels determined', [
            'contribution_id' => $this->contribution->id,
            'payment_method' => $this->contribution->payment_method,
            'channels' => $channels,
            'has_email' => !empty($this->contribution->email),
            'has_phone' => !empty($this->contribution->phone),
        ]);

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $donorName = $this->getDonorName();
        $donationLink = $this->getDonationLink();
        $shareMessage = $this->getShareMessage();
        $paymentMethod = $this->getPaymentMethodDisplay();

        return (new MailMessage)
            ->subject('Thank You for Your Generous Donation! 🙏')
            ->greeting("Hello {$donorName},")
            ->line('Thank you for your generous donation! Your contribution has been successfully processed.')
            ->line("**Donation Amount:** {$this->contribution->currency} " . number_format($this->contribution->amount, 2))
            ->line("**Payment Method:** {$paymentMethod}")
            ->line("**Transaction Reference:** {$this->getTransactionReference()}")
            ->line("**Date:** {$this->contribution->processed_at->format('F j, Y \a\t g:i A')}")
            ->line("**Donation Type:** " . ucfirst($this->contribution->donation_type))
            ->line('Your support makes a real difference and helps us continue our important work.')
            ->action('View Donation Details', $donationLink)
            ->line('**Share this donation link with others:**')
            ->line($donationLink)
            ->line($shareMessage)
            ->line('Thank you once again for your kindness and generosity!')
            ->salutation('With gratitude, The Team');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        $donorName = $this->getDonorName();
        $amount = $this->contribution->currency . ' ' . number_format($this->contribution->amount, 2);
        $donationLink = $this->getDonationLink();
        $paymentMethod = $this->getPaymentMethodDisplay();

        return "Dear {$donorName}, thank you for your generous donation of {$amount} via {$paymentMethod}! " .
            "Your contribution has been successfully processed. " .
            "Transaction: {$this->getTransactionReference()}. " .
            "Share this link to help others donate: {$donationLink}. " .
            "Your support makes a real difference!";
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsapp($notifiable)
    {
        $donorName = $this->getDonorName();
        $amount = $this->contribution->currency . ' ' . number_format($this->contribution->amount, 2);
        $donationLink = $this->getDonationLink();
        $transactionRef = $this->getTransactionReference();
        $paymentMethod = $this->getPaymentMethodDisplay();

        return "🙏 Dear {$donorName},\n\n" .
            "Thank you for your generous donation of {$amount}!\n\n" .
            "✅ Payment processed successfully via {$paymentMethod}\n" .
            "📝 Transaction: {$transactionRef}\n" .
            "📅 Date: {$this->contribution->processed_at->format('M j, Y g:i A')}\n\n" .
            "Your support makes a real difference and helps us continue our important work.\n\n" .
            "💝 Share this donation link to help others contribute:\n{$donationLink}\n\n" .
            "Thank you for your kindness and generosity! 🌟";
    }

    /**
     * Get donor name with fallback logic
     */
    protected function getDonorName(): string
    {
        // Try to get full name from billing information
        if ($this->contribution->bill_to_forename || $this->contribution->bill_to_surname) {
            $name = trim($this->contribution->bill_to_forename . ' ' . $this->contribution->bill_to_surname);
            if (!empty($name) && $name !== ' ') {
                return $name;
            }
        }

        // Fallback to generating name from email
        if ($this->contribution->email) {
            $emailParts = explode('@', $this->contribution->email);
            return ucfirst($emailParts[0]);
        }

        // Final fallback based on donation type
        return match ($this->contribution->donation_type) {
            'family' => 'Dear Family Member',
            'friend' => 'Dear Friend',
            'colleague' => 'Dear Colleague',
            'supporter' => 'Dear Supporter',
            'anonymous' => 'Dear Generous Donor',
            default => 'Dear Donor'
        };
    }

    /**
     * Get the donation link for sharing
     */
    protected function getDonationLink(): string
    {
        return route('donation.show', $this->contribution->donationLink->code);
    }

    /**
     * Get transaction reference for display (universal for all payment methods)
     */
    protected function getTransactionReference(): string
    {
        // CyberSource transaction ID
        if ($this->contribution->cybersource_transaction_id) {
            return $this->contribution->cybersource_transaction_id;
        }

        // CyberSource request ID
        if ($this->contribution->cybersource_request_id) {
            return $this->contribution->cybersource_request_id;
        }

        // M-Pesa receipt number (if you have this field)
        if ($this->contribution->transactions()->where('gateway', 'mpesa')->exists()) {
            $mpesaTransaction = $this->contribution->transactions()->where('gateway', 'mpesa')->first();
            if ($mpesaTransaction && !empty($mpesaTransaction->mpesa_receipt_number)) {
                return $mpesaTransaction->mpesa_receipt_number;
            }
        }

        // Fallback to generic donation reference
        return "DON-{$this->contribution->id}";
    }

    /**
     * Get payment method display name
     */
    protected function getPaymentMethodDisplay(): string
    {
        return match ($this->contribution->payment_method) {
            'card' => 'Credit/Debit Card',
            'mpesa' => 'M-Pesa',
            default => ucfirst($this->contribution->payment_method)
        };
    }

    /**
     * Get share message for the donation link
     */
    protected function getShareMessage(): string
    {
        $linkTitle = $this->contribution->donationLink->title ?? 'Make a Donation';

        return "Help us reach more people by sharing this donation link: \"{$linkTitle}\". " .
            "Every contribution, no matter the size, makes a meaningful impact. " .
            "Together, we can make a difference!";
    }

    /**
     * Determine if notification should be sent
     */
    public function shouldSend($notifiable, $channel): bool
    {
        // Only send if contribution is completed
        if ($this->contribution->payment_status !== \App\Models\Contribution::STATUS_COMPLETED) {
            Log::info('Skipping notification - contribution not completed', [
                'contribution_id' => $this->contribution->id,
                'status' => $this->contribution->payment_status,
                'channel' => $channel
            ]);
            return false;
        }

        // Check if we have the required contact information for the channel
        if (in_array($channel, ['sms', 'whatsapp']) && empty($this->contribution->phone)) {
            Log::info('Skipping SMS/WhatsApp notification - no phone number', [
                'contribution_id' => $this->contribution->id,
                'channel' => $channel
            ]);
            return false;
        }

        if ($channel === 'mail' && empty($this->contribution->email)) {
            Log::info('Skipping email notification - no email address', [
                'contribution_id' => $this->contribution->id,
                'channel' => $channel
            ]);
            return false;
        }

        return true;
    }
}
