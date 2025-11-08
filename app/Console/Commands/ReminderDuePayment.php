<?php

namespace App\Console\Commands;

use App\Jobs\DispatchUnpaidDue;
use App\Models\Due;
use Illuminate\Console\Command;

class ReminderDuePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-due-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dues = Due::selectRaw('house_id')
        ->where('status', 'unpaid')->groupBy('house_id')->get();

        foreach ($dues as $key => $item) {
            DispatchUnpaidDue::dispatch(
                $item->house_id
            )->onQueue('notifications');

        }
    }
}
