<?php

namespace Database\Seeders;

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

            ProvinceSeeder::class,
            DistrictSeeder::class,
            SubdistrictSeeder::class,
            RolePermissionSeeder::class,
            // FamilySeeder::class,
            ComplaintStatusSeeder::class,
            ComplaintCategorySeeder::class,
            // LargeFamilySeeder::class,
            FinancialCategorySeeder::class,
            VillageSeeder::class,
            CitizenSeeder::class,
            // FeeSeeder::class,
            // DueSeeder::class,
            // PatrolingSeeder::class,
            // ComplaintSeeder::class,
            // PatrolingSeeder::class,
=======
            ProvinceSeeder::class,
            DistrictSeeder::class,
            RolePermissionSeeder::class,
            ComplaintStatusSeeder::class,
            ComplaintCategorySeeder::class,
            FinancialCategorySeeder::class,
            FamilySeeder::class,
            LargeFamilySeeder::class,
            FeeSeeder::class,
            DueSeeder::class,
            PatrolingSeeder::class,
            ComplaintSeeder::class,
            UserSeeder::class
>>>>>>> 3e10734edaa76f00959619efda7aee555dc256f1
        ]);
    }
}
