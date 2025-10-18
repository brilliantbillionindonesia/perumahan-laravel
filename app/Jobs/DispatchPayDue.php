<?php

namespace App\Jobs;

use App\Http\Services\PushService;
use App\Models\Citizen;
use App\Models\House;
use App\Models\HousingUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchPayDue implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $houseId, public string $transactionCode)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PushService $push): void
    {
        $house = House::where('id', $this->houseId)->first();
        $citizenIds = Citizen::where('family_card_id', $house->family_card_id)->pluck('id')->toArray();
        $housingUsers = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $house->housing_id)
            ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
            ->where('housing_users.is_active', 1)
            ->whereIn('citizen_id', $citizenIds)->get();

        if ($housingUsers->isEmpty()) {
            return;
        }

        $tokens = $housingUsers->pluck('token')->filter()->all();

        $push->sendNotification(
            tokens: $tokens,
            title: 'Informasi Pembayaran Iuran',
            body: 'Informasi pembayaran iuran sudah tersedia • Tap untuk buka',
            channel: 'notification_channel',
            data: [
                'type' => 'due_payment',
                'housing_id' => $house->housing_id,
                'id' => $house->id,
                'transaction_code' => $this->transactionCode,
            ],
        );
    }
}
