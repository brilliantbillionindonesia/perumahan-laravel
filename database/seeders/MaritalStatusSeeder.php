<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\MaritalStatus;

class MaritalStatusSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('data/pernikahan');
        $files = File::files($path);
        
        foreach ($files as $file) {
            $json = File::get($file->getPathname());
            $data = json_decode($json, true);
        
            foreach ($data as $item) {
                \App\Models\MaritalStatus::updateOrCreate(
                    ['code' => $item['id']], // gunakan code sebagai unique key
                    [
                        'name' => $item['name'],
                    ]
                );
            }
        }
    }
}