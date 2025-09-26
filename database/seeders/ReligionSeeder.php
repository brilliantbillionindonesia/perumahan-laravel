<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/kepercayaan');
        $files = File::files($path);

        foreach ($files as $file) {
            $json = File::get($file->getPathname());
            $data = json_decode($json, true);

            foreach ($data as $item) {
                \App\Models\Religion::updateOrCreate(['code' => $item['code']], ['name' => $item['name']]);
            }
        }
    }
}
