<?php

namespace App\Jobs;

use App\Http\Services\NotificationService;
use App\Http\Services\PushService;
use App\Models\Complaint;
use App\Models\HousingUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchComplaintAction implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $complaintId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(PushService $push): void
    {
        $complaint = Complaint::where('id', $this->complaintId)->first();
        $userToken = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $complaint->housing_id)
        ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
        ->where('housing_users.user_id', '=', $complaint->user_id)
        ->where('housing_users.is_active', 1)
        ->get();

        $name = $complaint->updatedBy ? $complaint->updatedBy->name : 'Pengurus';

        $tokens = $userToken->pluck('token')->filter()->all();

        $type = "complaint";
        $title = "Pembaharuan Status Aduan";
        $channel = "notification_channel";
        $data =  [
            'type'          => $type,
            'housing_id'    => $complaint->housing_id,
            'id'            => $complaint->id,
            'title'         => substr($complaint->title, 0, 20),
            'description'   => substr($complaint->description, 0, 50),
            'name'          => $name,
            'created_at'    => $complaint->updated_at->toIso8601String(),
        ];


        $push->sendNotification(
            tokens: $tokens,
            title:  $title,
            body:  'Pembaharuan status aduan • Tap untuk buka',
            channel : $channel,
            data: $data
        );

        $notificationService = new NotificationService();
        $notificationService->sendNotification(
            $complaint->housing_id,
            'private',
            $title,
            "Pembaharuan status aduan",
            $channel,
            $data,
            $userToken
        );

    }
}
