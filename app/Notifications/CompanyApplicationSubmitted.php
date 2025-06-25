<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $company;
    protected $applicationNumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, $applicationNumber)
    {
        $this->company = $company;
        $this->applicationNumber = $applicationNumber;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Company Donation Application Submitted')
            ->greeting('Dear ' . $this->company->company_name . ',')
            ->line('Your company donation application has been successfully submitted.')
            ->line('Application Number: ' . $this->applicationNumber)
            ->line('Company Name: ' . $this->company->company_name)
            ->line('Contribution Name: ' . $this->company->contribution_name)
            ->line('We will review your application and get back to you soon.')
            ->line('Thank you for your interest in contributing!')
            ->salutation('Best regards, The Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'company_id' => $this->company->id,
            'application_number' => $this->applicationNumber,
            'company_name' => $this->company->company_name,
        ];
    }
}
