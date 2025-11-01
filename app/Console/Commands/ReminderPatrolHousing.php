<?php

namespace App\Console\Commands;

use App\Jobs\DispatchPatrolHousing;
use App\Models\Housing;
use App\Models\Patroling;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReminderPatrolHousing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-patrol-housing';

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
        $housings = Housing::select('id')->get();
        $date = Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d');
        foreach ($housings as $housing) {
            $currentPatrol = Patroling::where('housing_id', $housing->id)
            ->where('patrol_date', $date);
            $existPatrols = $currentPatrol->first();
            if($existPatrols){
                DispatchPatrolHousing::dispatch(
                    $existPatrols->id
                )->onQueue('notifications');
            }
        }
    }
}
