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
            DistrictSeeder::class,
            ProvinceSeeder::class,
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
=======
            ComplaintSeeder::class
>>>>>>> d93961c11114748d72baed31e41b1bb325c5f0a0
        ]);
    }
}
