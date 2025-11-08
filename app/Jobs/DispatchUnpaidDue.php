<?php

namespace App\Jobs;

use App\Http\Services\NotificationService;
use App\Http\Services\PushService;
use App\Models\Citizen;
use App\Models\House;
use App\Models\HousingUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchUnpaidDue implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $houseId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PushService $push): void
    {
        $house = House::where('id', operator: $this->houseId)->first();
        $citizens = Citizen::where('family_card_id', $house->family_card_id)->get();
        $citizenIds =  $citizens->pluck('id')->toArray();
        $housingUsers = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $house->housing_id)
            ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
            ->whereIn('citizen_id', $citizenIds)
            ->where('housing_users.is_active', 1)
            ->get();

        if ($housingUsers->isEmpty()) {
            return;
        }

        $tokens = $housingUsers->pluck('token')->filter()->all();
        $channel = 'notification_channel';
        $title = 'Pengingat Iuran Bulanan';
        $data = [
            'type' => 'due',
            'housing_id' => $house->housing_id,
            'id' => $house->id,
        ];

        $push->sendNotification(
            tokens: $tokens,
            title: $title,
            body: 'Iuran bulan ini belum kamu lunasi. Iuranmu membantu menjaga keamanan dan kenyamanan lingkungan kita bersama! • Tap untuk lihat detail',
            channel: $channel,
            data: [
                'type' => 'due',
                'housing_id' => $house->housing_id,
                'id' => $house->id,
            ],
        );

        $notificationService = new NotificationService();
        $notificationService->sendNotification(
            $house->housing_id,
            'public',
            $title,
            "Iuran bulan ini belum kamu lunasi. Iuranmu membantu menjaga keamanan dan kenyamanan lingkungan kita bersama!.",
            $channel,
            $data,
            $housingUsers
        );

    }
}
