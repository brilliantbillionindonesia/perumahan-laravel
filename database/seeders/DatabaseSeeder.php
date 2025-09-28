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
        // $this->call([VillageSeeder::class]);
        // $this->call([SubdistrictSeeder::class]);
        $this->call([DistrictSeeder::class]);
        $this->call([ProvinceSeeder::class]);
        $this->call([RolePermissionSeeder::class]);
        $this->call([FamilySeeder::class]);
    }
}
