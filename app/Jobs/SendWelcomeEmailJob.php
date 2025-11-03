<?php

// app/Jobs/SendWelcomeEmailJob.php
namespace App\Jobs;

use App\Mail\LinkToHousingMail;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int|string $userId, public string $password, public bool $isNewUser, public string $housingName) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        $password = $this->password;
        $housingName = $this->housingName;
        if (!$user) return;
        if($this->isNewUser){
            Mail::to($user->email)->queue(new WelcomeUserMail($user, $password, $housingName));
        } else {
            Mail::to($user->email)->queue(new LinkToHousingMail($user, $password, $housingName));
        }
    }
}
