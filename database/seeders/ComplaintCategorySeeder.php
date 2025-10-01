<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/category_complaint');
        $files = File::files($path);

        foreach ($files as $file) {
            $json = file_get_contents($file->getPathname());
            $data = json_decode($json, true);

            // kalau JSON hanya 1 object, bungkus ke array
            if (isset($data['id'])) {
                $data = [$data];
            }

            foreach ($data as $category) {
                DB::table('complaint_categories')->updateOrInsert(
                    ['code' => $category['code']], // unik berdasarkan code
                    [
                        'name' => $category['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            }
        }
    }
}
