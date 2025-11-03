<?php

// app/Mail/WelcomeUserMail.php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LinkToHousingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $password, public string $housingName) {}

    public function build(): LinkToHousingMail
    {
        return $this->subject('Akun Anda Berhasil Disinkronkan')
            ->markdown(view: 'emails.users.user-sync-housing')
            ->with([
                'user' => $this->user,
                'housing_name' => $this->housingName,
                'password' => $this->password,
            ]);
    }
}
