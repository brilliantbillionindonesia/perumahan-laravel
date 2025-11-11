<?php

// app/Mail/WelcomeUserMail.php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredDemo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): UserRegisteredDemo
    {

        return $this->subject('1 user telah terdaftar di ' . config('app.name'))
            ->markdown(view: 'emails.users.user-registered-demo')
            ->with([
                'user' => $this->user,
            ]);
    }
}
