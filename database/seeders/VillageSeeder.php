<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Village;

class VillageSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('data/kelurahan');
        $files = File::files($path);

        foreach ($files as $file) {
            $json = file_get_contents($file->getPathname());
            $data = json_decode($json, true);

            // kalau JSON hanya 1 object, bungkus ke array
            if (isset($data['id'])) {
                $data = [$data];
            }

            foreach ($data as $item) {
                $villageCode     = $item['id'];
                $subdistrictCode = substr($villageCode, 0, 6); // 6 digit pertama
                $districtCode    = substr($villageCode, 0, 4); // 4 digit pertama
                $provinceCode    = substr($villageCode, 0, 2); // 2 digit pertama

                // Format nama: huruf pertama kapital, sisanya kecil
                $formattedName = ucwords(strtolower($item['nama']));

                Village::updateOrCreate(
                    ['code' => $villageCode],
                    [
                        'name'             => $formattedName,
                        'province_code'    => $provinceCode,
                        'district_code'    => $districtCode,
                        'subdistrict_code' => $subdistrictCode,
                    ]
                );
            }
        }
    }
}