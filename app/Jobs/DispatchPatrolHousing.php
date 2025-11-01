<?php

namespace App\Jobs;

use App\Http\Services\NotificationService;
use App\Http\Services\PushService;
use App\Models\Citizen;
use App\Models\House;
use App\Models\HousingUser;
use App\Models\Patroling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Expr\Cast\Object_;

class DispatchPatrolHousing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $patrolId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PushService $push): void
    {
        $patrol = Patroling::where('id', $this->patrolId)->first();
        $housingUsers = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $patrol->housing_id)
            ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
            ->where('housing_users.is_active', 1)
            ->get();

        if ($housingUsers->isEmpty()) {
            return;
        }

        $tokens = $housingUsers->pluck('token')->filter()->all();
        $channel = 'notification_channel';
        $title = 'Ronda akan dimulai';
        $body = 'Sebentar lagi jadwal ronda akan dimulai. Cek siapa saja yang akan ronda.';
        $data = [
            'type' => 'patrol',
            'housing_id' => $patrol->housing_id,
            'date' => $patrol->patrol_date
        ];

        $push->sendNotification(
            tokens: $tokens,
            title: $title,
            body: $body,
            channel: $channel,
            data: $data
        );

        $notificationService = new NotificationService();
        $notificationService->sendNotification(
            $patrol->housing_id,
            'public',
            $title,
            $body,
            $channel,
            $data,
            $housingUsers
        );

    }
}
