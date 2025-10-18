<?php

// app/Jobs/SendWelcomeEmailJob.php
namespace App\Jobs;

use App\Mail\GeneratedPasswordMail;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendGeneratedPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int|string $userId, public string $password) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        $password = $this->password;
        if (!$user) return;
        Mail::to($user->email)->send(new GeneratedPasswordMail($user, $password));
    }
}
