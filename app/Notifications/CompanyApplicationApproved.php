<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $company;
    protected $donationLink;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $company, $donationLink)
    {
        $this->application = $application;
        $this->company = $company;
        $this->donationLink = $donationLink;
    }

    /**
     * Get the company instance.
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // Companies only get email notifications
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Company Donation Application Has Been Approved!')
            ->greeting("Hello {$this->company->company_name},")
            ->line('Congratulations! Your company donation application has been approved.')
            ->line("Application Number: {$this->application->application_number}")
            ->line('All your supporting documents have been successfully verified.')
            ->line('Your company can now proceed with the donation using the secure link below:')
            ->action('Make Corporate Donation', $this->donationLink->full_url)
            ->line('This donation link is secure and unique to your application.')
            ->line('Thank you for choosing our platform for your corporate giving!');
    }
}
