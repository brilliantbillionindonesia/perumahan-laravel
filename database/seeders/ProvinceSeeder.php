<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        // Lokasi file JSON
        $path = database_path('data/provinsi');

        $files = File::files($path);

        foreach ($files as $file) {
            $json = file_get_contents($file->getPathname());
            $data = json_decode($json, true);

            // kalau JSON hanya 1 object, bungkus ke array
            if (isset($data['id'])) {
                $data = [$data];
            }

        foreach ($data as $item) {
            $provinceCode = $item['id'] ?? null;
            $provinceName = $item['nama'] ?? null;

            $formattedName = ucwords(strtolower($provinceName));

            if (!$provinceCode || !$provinceName) {
                continue; // skip jika datanya tidak lengkap
            }

            Province::updateOrCreate(
                ['code' => $provinceCode], // berdasarkan kode unik provinsi
                ['name' => $formattedName]
            );
        }

        }
    }
}
