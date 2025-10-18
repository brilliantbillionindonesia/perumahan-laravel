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

            // Kalau JSON hanya 1 object, bungkus ke array
            if (isset($data['id'])) {
                $data = [$data];
            }

            // Ambil kode kabupaten dari nama file
            $districtCode = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            foreach ($data as $item) {
                // Ambil 2 digit pertama dari district_code → province_code
                $provinceCode = substr($districtCode, 0, 2);

                // Format nama: hanya huruf pertama kapital, sisanya huruf kecil
                $formattedName = ucwords(strtolower($item['nama']));

                Subdistrict::updateOrCreate(
                    ['code' => $item['id']], // JSON "id" → DB "code"
                    [
                        'name'          => $formattedName,  // Format huruf diperbaiki
                        'district_code' => $districtCode,    // Nama file → district_code
                        'province_code' => $provinceCode,    // Ambil otomatis dari district_code
                    ]
                );
            }
        }
    }
}
