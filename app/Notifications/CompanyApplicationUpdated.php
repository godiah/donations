<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyApplicationUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $company;
    protected $applicationNumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, string $applicationNumber)
    {
        $this->company = $company;
        $this->applicationNumber = $applicationNumber;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Application Update Confirmation - ' . $this->applicationNumber)
            ->greeting('Hello ' . $this->company->company_name . ',')
            ->line('Your application (' . $this->applicationNumber . ') has been successfully updated.')
            ->line('The updated details have been submitted for re-review. You will be notified once the review is complete.')
            ->action('View Application', route('company.applications.show', $this->applicationNumber))
            ->line('Thank you for your submission.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'application_number' => $this->applicationNumber,
            'company_name' => $this->company->company_name,
            'message' => 'Application ' . $this->applicationNumber . ' has been updated and submitted for re-review.',
        ];
    }
}