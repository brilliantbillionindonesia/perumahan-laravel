<?php

namespace Database\Seeders;

use App\Models\Fee;
use App\Models\Housing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $housings = Housing::all();

        foreach ($housings as $housing) {
            Fee::create([
                'housing_id' => $housing->id,
                'financial_category_code' => 'iuran',
                'name' => 'Iuran Sampah',
                'due_day' => 10,
                'amount' => 15000,
                'frequency' => 'recurring',
            ]);

            Fee::create([
                'housing_id' => $housing->id,
                'financial_category_code' => 'iuran',
                'name' => 'Iuran Keamanan',
                'due_day' => 10,
                'amount' => 10000,
                'frequency' => 'recurring',
            ]);
        }
    }
}
