<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialCategory;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'iuran',
                'name' => 'Iuran',
                'type' => 'income',
            ], [
                'code' => 'donasi',
                'name' => 'Donasi',
                'type' => 'income',
            ], [
                'code' => 'bantuan_pemerintah',
                'name' => 'Bantuan Pemerintah',
                'type' => 'income',
            ], [
                'code' => 'kegiatan_sosial',
                'name' => 'Kegiatan Sosial',
                'type' => 'expense',
            ], [
                'code' => 'kebersihan',
                'name' => 'Kebersihan',
                'type' => 'expense',
            ], [
                'code' => 'keamanan',
                'name' => 'Keamanan',
                'type' => 'expense',
            ]
        ];

        foreach ($data as $item) {
            FinancialCategory::create($item);
        }
    }
}
