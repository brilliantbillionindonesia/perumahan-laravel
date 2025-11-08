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

        $security = Role::create([
            'code' => 'security',
            'name' => 'Satpam',
        ]);

        $manageUsers = Permission::create([
            'code' => 'manage_users',
            'name' => 'Mengatur Pengguna',
        ]);

        $manageCitizens = Permission::create([
            'code' => 'manage_citizens',
            'name' => 'Mengatur Warga',
        ]);

        $manageTransaction = Permission::create([
            'code' => 'manage_transactions',
            'name' => 'Mengatur Transaksi',
        ]);

        $manageComplaint = Permission::create([
            'code' => 'manage_complaints',
            'name' => 'Mengatur Pengaduan',
        ]);

        $managePatrol = Permission::create([
            'code' => 'manage_patrols',
            'name' => 'Mengatur Ronda',
        ]);

        $viewReports = Permission::create([
            'code' => 'view_reports',
            'name' => 'Melihat Laporan',
        ]);

        $viewFinancialReport = Permission::create([
            'code' => 'view_financial_reports',
            'name' => 'Melihat Laporan Keuangan',
        ]);

        $viewTransactions = Permission::create([
            'code' => 'view_transactions',
            'name' => 'Melihat Transaksi',
        ]);

        $admin->permissions()->attach([
            $manageCitizens->code,
            $manageUsers->code,
            $manageTransaction->code,
            $manageComplaint->code,
            $managePatrol->code,
            $viewReports->code,
            $viewFinancialReport->code,
            $viewTransactions->code
        ]);

        $manager->permissions()->attach([
            $manageCitizens->code,
            $manageUsers->code,
            $manageTransaction->code,
            $manageComplaint->code,
            $managePatrol->code,
            $viewReports->code,
            $viewFinancialReport->code,
            $viewTransactions->code
        ]);

        $secretary->permissions()->attach([
            $manageCitizens->code,
            $manageTransaction->code,
            $manageComplaint->code,
            $managePatrol->code,
            $viewReports->code,
            $viewFinancialReport->code,
            $viewTransactions->code
        ]);

        $bendahara->permissions()->attach([
            $manageCitizens->code,
            $manageTransaction->code,
            $viewReports->code,
            $viewFinancialReport->code,
            $viewTransactions->code
        ]);

        $citizen->permissions()->attach([
            $viewReports->code,
            $viewFinancialReport->code,
            $viewTransactions->code
        ]);
    }
}
