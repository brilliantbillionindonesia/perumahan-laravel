<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\WorkType;

class WorkSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('data/pekerjaan');
        $files = File::files($path);

        foreach ($files as $file) {
            $json = File::get($file->getPathname());
            $data = json_decode($json, true);

            foreach ($data as $item) {
                \App\Models\WorkType::updateOrCreate(['code' => $item['code']], ['name' => $item['name']]);
            }
        }
    }
}
