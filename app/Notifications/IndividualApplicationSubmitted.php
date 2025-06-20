<?php

namespace App\Notifications;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class IndividualApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $individual;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $individual)
    {
        $this->application = $application;
        $this->individual = $individual;
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
            ->subject('Your Donation Application Has Been Submitted')
            ->greeting("Hello {$this->individual->full_name},")
            ->line('Thank you for submitting your individual donation application.')
            ->line("**Application Number:** {$this->application->application_number}")
            ->line("**Contribution Name:** {$this->individual->contribution_name}")
            ->line('We will review your application and get back to you soon.')
            ->action('View Application', route('individual.success', $this->application))
            ->line('Thank you for using our platform!');
    }


    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        return "Dear {$this->individual->full_name}, your donation application ({$this->application->application_number}) has been submitted successfully. We'll review it soon. Thank you!";
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsapp($notifiable)
    {
        return "Dear {$this->individual->full_name}, your donation application ({$this->application->application_number}) has been submitted successfully. We'll review it soon. Visit " . route('individual.success', $this->application) . " for details. Thank you!";
    }
}
