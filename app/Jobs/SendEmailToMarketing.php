<?php

// app/Jobs/SendWelcomeEmailJob.php
namespace App\Jobs;

use App\Mail\LinkToHousingMail;
use App\Mail\UserRegisteredDemo;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailToMarketing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int|string $userId) {}

    public function handle(): void
    {
        $emails = [
            'brilliantbillionindonesia@gmail.com',
            'ariumboroseno@gmail.com',
            'masdit966@gmail.com'
        ];
        $user = User::find($this->userId);
        if (!$user) return;
        foreach ($emails as $email) {
            Mail::to($email)->queue(new UserRegisteredDemo($user));
        }
    }
}
