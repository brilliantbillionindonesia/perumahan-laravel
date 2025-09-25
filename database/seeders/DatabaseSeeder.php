<?php

namespace Database\Seeders;

use App\Models\Housing;
use App\Models\HousingUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([SubdistrictSeeder::class]);
        $this->call([RolePermissionSeeder::class]);

        $createdUser = User::factory()->create([
            'name' => 'Admin Mustika',
            'email' => 'admin-mustika@mail.com',
            'password' => bcrypt('password'),
        ]);

        $createdUser2 = User::factory()->create([
            'name' => 'Admin Grisent',
            'email' => 'admin-grisen@mail.com',
            'password' => bcrypt('password'),
        ]);

        $createdHousing = Housing::create([
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

        $createdHousing2 = Housing::create([
            'housing_name' => 'Griya Sentosa',
            'address' => 'Jl. Griya Sentosa',
            'rt' => '01',
            'rw' => '01',
            'village_code' => '01',
            'subdistrict_code' => '01',
            'district_code' => '01',
            'province_code' => '01',
            'postal_code' => '41352',
        ]);

        $createdHousing3 = Housing::create([
            'housing_name' => 'Brilliant Appartment',
            'address' => 'Jl. Brilliant Billion Indonesia',
            'rt' => '01',
            'rw' => '01',
            'village_code' => '01',
            'subdistrict_code' => '01',
            'district_code' => '01',
            'province_code' => '01',
            'postal_code' => '41352',
        ]);

        HousingUser::create([
            'housing_id' => $createdHousing->id,
            'user_id' => $createdUser->id,
            'role_code' => 'admin',
        ]);

        HousingUser::create([
            'housing_id' => $createdHousing2->id,
            'user_id' => $createdUser->id,
            'role_code' => 'citizen',
        ]);

        HousingUser::create([
            'housing_id' => $createdHousing->id,
            'user_id' => $createdUser2->id,
            'role_code' => 'citizen',
        ]);

        HousingUser::create([
            'housing_id' => $createdHousing2->id,
            'user_id' => $createdUser2->id,
            'role_code' => 'admin',
        ]);
    }
}
