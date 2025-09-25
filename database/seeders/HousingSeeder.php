<?php

namespace Database\Seeders;

use App\Models\Housing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HousingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Housing::create([
            'housing_name' => 'Mustika Village Karawang',
            'address' => 'Jl. Mustika Karawang',
            'rt' => '01',
            'rw' => '01',
            'village_code' => '01',
            'subdistrict_code' => '01',
            'district_code' => '01',
            'province_code' => '01',
            'postal_code' => '41351',
        ]);
    }
}
