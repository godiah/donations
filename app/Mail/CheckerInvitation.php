<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckerInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this->subject('Complete Your Account Setup - Payout Checker Invitation')
            ->view('emails.checker-invitation')
            ->with([
                'invitationUrl' => route('invitation.register', $this->invitation->token),
                'applicationNumber' => $this->invitation->application->application_number,
                'makerName' => $this->invitation->application->creator()?->name ?? 'Unknown',

                'expiresAt' => $this->invitation->expires_at,
            ]);
    }
}
