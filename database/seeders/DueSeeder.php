<?php

namespace Database\Seeders;

use App\Models\CashBalance;
use App\Models\Due;
use App\Models\Fee;
use App\Models\House;
use App\Models\Housing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $housing = Housing::all();

        foreach ($housing as $h) {
            $houses = House::where('housing_id', $h->id)->get();
            $fees = Fee::where('housing_id', $h->id)->get();
            foreach ($houses as $house) {
                foreach ($fees as $fee) {
                    $due = new Due();
                    $due->housing_id = $h->id;
                    $due->house_id = $house->id;
                    $due->fee_id = $fee->id;
                    $due->amount = $fee->amount;
                    $due->status = 'unpaid';
                    $due->periode = date('Y-m-d');
                    $due->save();
                }
            }

            $cashBalance = new CashBalance();
            $cashBalance->housing_id = $h->id;
            $cashBalance->year = date('Y');
            $cashBalance->month = date('m');
            $cashBalance->save();

        }
    }
}
