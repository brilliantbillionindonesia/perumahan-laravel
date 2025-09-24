<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Subdistrict;

class SubdistrictSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('data/kecamatan');
        $files = File::files($path);

        foreach ($files as $file) {
            $json = file_get_contents($file->getPathname());
            $data = json_decode($json, true);

            // kalau JSON hanya 1 object, bungkus ke array
            if (isset($data['id'])) {
                $data = [$data];
            }

            // ambil kode kabupaten dari nama file
            $districtCode = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            foreach ($data as $item) {
                Subdistrict::updateOrCreate(
                    ['code' => $item['id']], // JSON "id" → DB "code"
                    [
                        'name'          => $item['nama'],       // JSON "nama" → DB "name"
                        'district_code' => $districtCode,       // nama file → district_code
                    ]
                );
            }
        }
    }
}