<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignupSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Welcome to the Platform!')
            ->view('emails.signup-success')
            ->with([
                'name' => $this->user->name,
                'userType' => ucfirst(is_string($this->user->user_type) ? $this->user->user_type : $this->user->user_type->value),
            ]);
    }
}
