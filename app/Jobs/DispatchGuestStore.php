<?php

namespace App\Jobs;

use App\Http\Services\NotificationService;
use App\Http\Services\PushService;
use App\Models\Complaint;
use App\Models\Guest;
use App\Models\House;
use App\Models\HousingUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchGuestStore implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $guestId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PushService $push): void
    {
        $guest = Guest::where('id', $this->guestId)->first();
        $house = House::where('id', $guest->house_id)->first();
        $management = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $guest->housing_id)
        ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
        ->where('housing_users.user_id', '!=', $guest->registered_by)
        ->get();

        if($management->isEmpty()){
            return;
        }

        $name = $guest->name;

        $tokens = $management->pluck('token')->filter()->all();

        $type = "guest";
        $title = "Tamu Baru";
        $channel = "notification_channel";
        $data = [
            'type'          => $type,
            'housing_id'    => $guest->housing_id,
            'id'            => $guest->id,
            'title'         => $title,
            'name'          => $name,
            'created_at'    => $guest->created_at->toIso8601String(),
        ];

        $push->sendNotification(
            tokens: $tokens,
            title: $title,
            body:  'Tamu baru • Tap untuk buka',
            channel : $channel,
            data: $data
        );

        $notificationService = new NotificationService();
        $notificationService->sendNotification(
            $guest->housing_id,
            'public',
            $title,
            "Tamu Baru di " . $house->block ." ".$house->number,
            $channel,
            $data,
            $management
        );
    }
}
