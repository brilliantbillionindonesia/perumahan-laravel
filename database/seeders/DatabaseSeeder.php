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
        ]);
    }
}
