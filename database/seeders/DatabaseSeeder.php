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
        ]);
    }
}
