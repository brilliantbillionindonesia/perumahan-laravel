<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Tulis log untuk bukti eksekusi
        Log::info('✅ TestJob dijalankan pada ' . now());

        // Optional: kirim email untuk bukti
        Mail::raw('Job berhasil dijalankan pada ' . now(), function ($m) {
            $m->to('ariumboroseno@gmail.com')
              ->subject('Tes Laravel Queue dari Serumpun Padi');
        });
    }
}
