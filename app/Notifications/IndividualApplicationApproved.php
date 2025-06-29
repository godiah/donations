<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndividualApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $individual;
    protected $donationLink;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $individual, $donationLink)
    {
        $this->application = $application;
        $this->individual = $individual;
        $this->donationLink = $donationLink;
    }

    /**
     * Get the individual instance.
     */
    public function getIndividual()
    {
        return $this->individual;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['mail'];

        // Add SMS and WhatsApp for individuals with phone numbers
        if ($this->individual->phone) {
            $channels[] = 'sms';
            $channels[] = 'whatsapp';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Donation Application Has Been Approved!')
            ->greeting("Hello {$this->individual->full_name},")
            ->line('Congratulations! Your individual donation application has been approved.')
            ->line("Application Number: {$this->application->application_number}")
            ->line('Your application and all supporting documents have been successfully verified.')
            ->line('You can now proceed with your donation using the secure link below:')
            ->action('Make Your Donation', $this->donationLink->full_url)
            ->line('This donation link is secure and unique to your application.')
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        return "Great news {$this->individual->full_name}! Your donation application ({$this->application->application_number}) has been APPROVED. Donate now: {$this->donationLink->full_url}";
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsapp($notifiable)
    {
        return "🎉 Congratulations {$this->individual->full_name}! Your donation application ({$this->application->application_number}) has been APPROVED. All documents verified successfully. Make your donation here: {$this->donationLink->full_url}";
    }
}
