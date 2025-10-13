<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('data/kabupaten');
        $files = File::files($path);

        foreach ($files as $file) {
            $json = file_get_contents($file->getPathname());
            $data = json_decode($json, true);

            // Kalau JSON hanya 1 object, bungkus ke array
            if (isset($data['id'])) {
                $data = [$data];
            }

            foreach ($data as $item) {
                $villageCode  = $item['id'];
                $districtCode = substr($villageCode, 0, 4); // 4 digit pertama
                $provinceCode = substr($villageCode, 0, 2); // 2 digit pertama

                // Format nama: hanya huruf pertama kapital, sisanya kecil
                $formattedName = ucwords(strtolower($item['name']));

                // Simpan district (kabupaten/kota)
                District::updateOrCreate(
                    ['code' => $districtCode],
                    [
                        'name'          => $formattedName,
                        'province_code' => $provinceCode,
                    ]
                );
            }
        }
    }
}