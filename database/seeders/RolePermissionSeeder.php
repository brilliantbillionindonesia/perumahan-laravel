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

        $secretary = Role::create([
            'code' => 'sekretaris',
            'name' => 'Sekretaris',
        ]);

        $bendahara = Role::create([
            'code' => 'bendahara',
            'name' => 'Bendahara',
        ]);

        $manager = Role::create([
            'code' => 'manager',
            'name' => 'Manager',
        ]);

        $citizen = Role::create([
            'code' => 'citizen',
            'name' => 'Warga',
        ]);

        $manageUsers = Permission::create([
            'code' => 'manage_users',
            'name' => 'Mengatur Pengguna',
        ]);

        $manageTransaction = Permission::create([
            'code' => 'manage_transactions',
            'name' => 'Mengatur Transaksi',
        ]);

        $manageComplaint = Permission::create([
            'code' => 'manage_complaints',
            'name' => 'Mengatur Pengaduan',
        ]);

        $viewReports = Permission::create([
            'code' => 'view_reports',
            'name' => 'Melihat Laporan',
        ]);

        $viewTransactions = Permission::create([
            'code' => 'view_transactions',
            'name' => 'Melihat Transaksi',
        ]);

        $admin->permissions()->attach([
            $manageUsers->code,
            $manageTransaction->code,
            $manageComplaint->code,
            $viewReports->code,
            $viewTransactions->code
        ]);

        $manager->permissions()->attach([
            $manageUsers->code,
            $manageTransaction->code,
            $manageComplaint->code,
            $viewReports->code,
            $viewTransactions->code
        ]);

        $secretary->permissions()->attach([
            $manageComplaint->code,
            $viewReports->code,
            $viewTransactions->code
        ]);

        $bendahara->permissions()->attach([
            $manageTransaction->code,
            $viewReports->code,
            $viewTransactions->code
        ]);

        $citizen->permissions()->attach([
            $viewReports->code,
            $viewTransactions->code
        ]);
    }
}
