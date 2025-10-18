<?php

// app/Mail/WelcomeUserMail.php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $password) {}

    public function build(): WelcomeUserMail
    {
        return $this->subject('Welcome to our app')
            ->markdown(view: 'emails.users.welcome')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
