<?php

namespace App\Jobs;

use App\Http\Services\PushService;
use App\Models\Complaint;
use App\Models\HousingUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchComplaintStore implements ShouldQueue
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
        $management = HousingUser::selectRaw('housing_users.user_id,token')->where('housing_id', $complaint->housing_id)
        ->join('device_tokens', 'device_tokens.user_id', '=', 'housing_users.user_id')
        ->where('role_code', '!=', 'citizen')->get();

        if($management->isEmpty()){
            return;
        }

        $name = $complaint->user ? $complaint->user->name : 'Warga';

        $tokens = $management->pluck('token')->filter()->all();

        $push->sendNotification(
            tokens: $tokens,
            title: 'Aduan Baru',
            body:  'Aduan baru dari ' . ($name ?? 'Warga') . ' • Tap untuk buka',
            channel : 'notification_channel',
            data: [
                'type'      => 'complaint',
                'housing_id' => $complaint->housing_id,
                'id'        => $complaint->id,
                'title'     => substr($complaint->title, 0, 20),
                'description' => substr($complaint->description, 0, 50),
                'name'      => $name,
                'created_at'=> $complaint->created_at->toIso8601String(),
            ],
        );


    }
}
