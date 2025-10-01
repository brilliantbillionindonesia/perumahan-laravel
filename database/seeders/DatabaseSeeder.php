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
<<<<<<< HEAD

=======
        $this->call([FamilySeeder::class]);
        $this->call([ComplaintStatusSeeder::class]);
        $this->call([ComplaintCategorySeeder::class]);
>>>>>>> ab4000606b4b5a23bd5e1c3507850aec6c85dc09
        $this->call([FamilySeeder::class]);
        $this->call([LargeFamilySeeder::class]);
<<<<<<< HEAD
        $this->call([ComplaintStatusSeeder::class]);
        $this->call([ComplaintCategorySeeder::class]);

=======
        $this->call([FamilySeeder::class]);
        $this->call([ComplaintStatusSeeder::class]);
        $this->call([ComplaintCategorySeeder::class]);
>>>>>>> 5fef556aba44a218547a3742eb5ab805c63db3ad
=======
>>>>>>> ab4000606b4b5a23bd5e1c3507850aec6c85dc09
    }
}
