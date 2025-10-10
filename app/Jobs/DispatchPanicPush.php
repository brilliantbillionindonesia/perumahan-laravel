<?php

namespace App\Jobs;

use App\Models\PanicEvent;
use App\Models\PanicRecipient;
use App\Http\Services\PushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchPanicPush implements ShouldQueue
{
    use Queueable, Dispatchable;

    public function __construct(public string $panicId) {}

    public function handle(PushService $push)
    {
        $panic = PanicEvent::with('recipients.user.devices')->findOrFail($this->panicId);


        foreach ($panic->recipients as $rec) {
            $tokens = $rec->user->devices->pluck('token')->filter()->all();
            if (empty($tokens)) continue;

            // kirim silent/high-priority + data
            $ok = $push->sendSilentData(
                tokens: $tokens,
                data: [
                    'type'      => 'panic',
                    'panic_id'  => $panic->id,
                    'name'      => $rec->user->name,
                    'lat'       => $panic->latitude,
                    'lng'       => $panic->longitude,
                    'created_at'=> $panic->created_at->toIso8601String(),
                ]
            );

            if ($ok) {
                $rec->update([
                    'status'       => 'delivered',
                    'delivered_at' => now(),
                ]);
            }
        }
    }
}
