<?php

// app/Mail/WelcomeUserMail.php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneratedPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $password) {}

    public function build()
    {
        return $this->subject('Password Baru')
            ->markdown(view: 'emails.users.generated-password')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
