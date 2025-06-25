<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckerInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invitation;
    protected $checkerEmail;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invitation $invitation, string $checkerEmail)
    {
        $this->invitation = $invitation;
        $this->checkerEmail = $checkerEmail;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['mail'];

        if ($notifiable->phone ?? false) {
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
            ->subject('Complete Your Account Setup - Payout Checker Invitation')
            ->view('emails.checker-invitation', [
                'invitationUrl' => route('invitation.register', $this->invitation->token),
                'applicationNumber' => $this->invitation->application->application_number,
                'makerName' => $this->invitation->application->user->name,
                'expiresAt' => $this->invitation->expires_at,
            ]);
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        return "Dear Checker, you've been invited to complete your account setup as a Payout Checker for application {$this->invitation->application->application_number}. Visit " . route('invitation.register', $this->invitation->token) . " before {$this->invitation->expires_at->format('d/m/Y')}. Thank you!";
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsapp($notifiable)
    {
        return "Dear Checker, you've been invited to complete your account setup as a Payout Checker for application {$this->invitation->application->application_number}. Please visit " . route('invitation.register', $this->invitation->token) . " to register before {$this->invitation->expires_at->format('d/m/Y')}. Thank you!";
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'invitation_id' => $this->invitation->id,
            'application_number' => $this->invitation->application->application_number,
            'expires_at' => $this->invitation->expires_at->toDateTimeString(),
        ];
    }
}
