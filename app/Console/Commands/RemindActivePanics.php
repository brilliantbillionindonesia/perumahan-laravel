<?php

// app/Console/Commands/RemindActivePanics.php
namespace App\Console\Commands;

use App\Http\Services\PushService;
use App\Models\PanicRecipient;
use Illuminate\Console\Command;

class RemindActivePanics extends Command
{
    protected $signature = 'panic:remind';
    protected $description = 'Kirim reminder panic untuk penerima yang belum menindak';

    public function handle(PushService $push)
    {
        // ambil semua penerima yang panic-nya masih aktif + belum read,
        // dan sudah lewat >= 60 detik dari reminder terakhir
        PanicRecipient::with('eventActive')
            ->where(function ($q) {
                $q->whereNull('last_reminded_at')
                    ->orWhere('last_reminded_at', '<=', now()->subSeconds(60));
            })
            // ->where('reminder_count', '<', 60)
            ->chunkById(200, function ($rows) use ($push) {
                foreach ($rows as $rec) {
                    $panic = $rec->eventActive;
                    $tokens = $rec->user->devices->pluck('token')->filter()->values()->all();
                    if (empty($tokens)) {
                        $rec->update(['last_reminded_at' => now(), 'reminder_count' => $rec->reminder_count + 1]);
                        continue;
                    }


                    $data = [
                        'type' => 'panic',
                        'panic_id' => $panic->id,
                        'name' => $panic->citizen->fullname ?? $panic->user->name,
                        'lat' => $panic->latitude,
                        'long' => $panic->longitude,
                        'created_at' => $panic->created_at->toIso8601String(),
                    ];

                    $ok = $push->sendPanic(
                        tokens: $tokens,
                        data: $data,
                        title: 'Permintaan PANIC!',
                        body: ($data['name'] ? "{$data['name']} masih membutuhkan bantuan." : 'Butuh bantuan segera.')
                    );

                    if ($ok) {
                        $rec->update([
                            'status' => $rec->status, // biarkan, atau tetap 'delivered'
                            'last_reminded_at' => now(),
                            'reminder_count' => $rec->reminder_count + 1,
                        ]);
                    }
                }
            });

        $this->info('Reminder panic diproses.');
        return Command::SUCCESS;
    }
}
