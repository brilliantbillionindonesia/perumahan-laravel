<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Housing;
use App\Models\HousingUser;
use App\Models\Patroling;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
<<<<<<< HEAD
            SubdistrictSeeder::class,
            DistrictSeeder::class,
            ProvinceSeeder::class,
            VillageSeeder::class,
=======
            ProvinceSeeder::class,
            DistrictSeeder::class,
            // SubdistrictSeeder::class,
            // VillageSeeder::class,
>>>>>>> b6d1a5abb3b161fe3255907d8e7b4ee696b9e407
            RolePermissionSeeder::class,
            FamilySeeder::class,
            ComplaintStatusSeeder::class,
            ComplaintCategorySeeder::class,
            LargeFamilySeeder::class,
            FinancialCategorySeeder::class,
            FeeSeeder::class,
            DueSeeder::class,
<<<<<<< HEAD

            // PatrolingSeeder::class,
            ComplaintSeeder::class

=======
            PatrolingSeeder::class,
            ComplaintSeeder::class
>>>>>>> b6d1a5abb3b161fe3255907d8e7b4ee696b9e407
        ]);
    }
}
