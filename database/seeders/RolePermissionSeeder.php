<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::create([
            'code' => 'admin',
            'name' => 'Admin',
        ]);

        $citizen = Role::create([
            'code' => 'citizen',
            'name' => 'Warga',
        ]);

        $manageUsers = Permission::create([
            'code' => 'manage_users',
            'name' => 'Mengatur Pengguna',
        ]);

        $viewReports = Permission::create([
            'code' => 'view_reports',
            'name' => 'Melihat Laporan',
        ]);

        $admin->permissions()->attach([
            $manageUsers->code,
            $viewReports->code,
        ]);

        $citizen->permissions()->attach([
            $viewReports->code,
        ]);
    }
}
