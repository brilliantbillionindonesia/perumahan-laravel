<?php

namespace Database\Seeders;

use App\Models\Complaint;
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
<<<<<<< HEAD

        $this->call([FamilySeeder::class]);
        $this->call([ComplaintStatusSeeder::class]);
        $this->call([ComplaintCategorySeeder::class]);
        // $this->call([FamilySeeder::class]);
        $this->call([LargeFamilySeeder::class]);

        // $this->call([FamilySeeder::class]);
        $this->call([LargeFamilySeeder::class]);
        $this->call([ComplaintStatusSeeder::class]);
        $this->call([ComplaintCategorySeeder::class]);

=======
        $this->call([FamilySeeder::class]);
        $this->call([ComplaintStatusSeeder::class]);
        $this->call([ComplaintCategorySeeder::class]);
>>>>>>> 5fef556aba44a218547a3742eb5ab805c63db3ad
    }
}
