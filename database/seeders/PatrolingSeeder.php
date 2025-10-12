<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\Housing;
use Illuminate\Database\Seeder;
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
            $houses = House::where('housing_id', $housingId)->get();

            foreach ($houses as $house) {
                $houseId = $house->id;
                $citizenId = $house->head_citizen_id;

                // Buat rondaFreq data dengan tanggal acak
                for ($i = 0; $i < $rondaFreq; $i++) {
                    // buat tanggal acak antara start & end
                    $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
                    $randomDate = Carbon::createFromTimestamp($randomTimestamp)->format('Y-m-d');

                    $dataPatrols[] = [
                        'housing_id'   => $housingId,
                        'citizen_id'   => $citizenId,
                        'house_id'     => $houseId,
                        'patrol_date'  => $randomDate,
                        'presence'     => 'hadir',
                        'note'         => null,
                        'replaced_by'  => null,
                        'deleted_at'   => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
            }
        }

        if (!empty($dataPatrols)) {
            DB::table('patrollings')->insert($dataPatrols);
            $this->command->info(' Berhasil menambahkan ' . count($dataPatrols) . ' data patroli dengan tanggal acak.');
        } else {
            $this->command->warn(' Tidak ada data housing/house untuk dibuatkan patroli.');
        }
    }
}