<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\PatrolingController;
use App\Models\House;
use App\Models\Housing;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatrolingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rondaFreq = 1; // jumlah ronda per rumah
        $dataPatrols = [];

        // rentang tanggal random (bisa disesuaikan)
        $startDate = Carbon::create(2025, 10, 1);
        $endDate   = Carbon::create(2025, 10, 31);

        $housings = Housing::select('id')->get();

        foreach ($housings as $housing) {
            $housingId = $housing->id;
            $patrolController = new PatrolingController();
            $request = new Request([
                'housing_id' => $housingId,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'frequency' => $rondaFreq
            ]);
            $patrolController->store($request);
        }
    }
}
