<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;
use App\Models\Individual;

class IndividualApplicationUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $individual;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application, Individual $individual)
    {
        $this->application = $application;
        $this->individual = $individual;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Application Updated Successfully')
            ->greeting('Hello ' . $this->individual->first_name . '!')
            ->line('Your individual donation application has been updated successfully.')
            ->line('Application Number: ' . $this->application->application_number)
            ->line('Contribution Name: ' . $this->individual->contribution_name)
            ->line('Your application has been resubmitted for review.')
            ->action('View Application', route('individual.applications.show', $this->application->application_number))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'contribution_name' => $this->individual->contribution_name,
            'type' => 'individual_application_updated',
            'message' => 'Your application has been updated and resubmitted for review.',
        ];
    }
}
