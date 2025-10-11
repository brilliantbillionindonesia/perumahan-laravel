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
        $panic = PanicEvent::with(['recipients.user.devices', 'citizen', 'user'])->findOrFail($this->panicId);

        $namePanic = $panic->citizen ? $panic->citizen->fullname : $panic->user->name;

        foreach ($panic->recipients as $rec) {
            $tokens = $rec->user->devices->pluck('token')->filter()->all();
            if (empty($tokens)) continue;

            $ok = $push->sendPanic(
                tokens: $tokens,
                title: 'Permintaan PANIC!',
                body:  'Dari ' . ($namePanic ?? 'Warga') . ' • Tap untuk buka',
                data: [
                    'type'      => 'panic',
                    'panic_id'  => $panic->id,
                    'name'      => $namePanic,
                    'lat'       => (string) $panic->latitude,
                    'lng'       => (string) $panic->longitude,
                    'created_at'=> $panic->created_at->toIso8601String(),
                ],
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
