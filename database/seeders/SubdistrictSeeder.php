<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Subdistrict;

class SubdistrictSeeder extends Seeder
{
    public function run()
    {
        // ✅ Kosongkan tabel sebelum isi ulang
        Subdistrict::truncate();

        $path = database_path('data/kecamatan');
        $files = File::files($path);

        foreach ($files as $file) {
            $districtCode = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // ✅ Lewati file yang bukan kode angka 4 digit (contoh: 1101.json)
            if (!preg_match('/^\d{4}$/', $districtCode)) {
                $this->command->warn("⚠️ Lewati file tidak valid: {$file->getFilename()}");
                continue;
            }

            $provinceCode = substr($districtCode, 0, 2);
            $json = file_get_contents($file->getPathname());
            $data = json_decode($json, true);

            if (empty($data)) {
                $this->command->warn("⚠️ File kosong: {$file->getFilename()}");
                continue;
            }

            if (isset($data['id'])) {
                $data = [$data];
            }

            echo "\n📂 Memproses file: {$file->getFilename()} (District: {$districtCode})";

            foreach ($data as $index => $item) {
                $formattedName = ucwords(strtolower($item['nama'] ?? 'Tanpa Nama'));

                // ✅ Ambil kode dari JSON kalau sudah ada
                $subdistrictCode = $item['kode'] ?? ($districtCode . str_pad($index + 1, 2, '0', STR_PAD_LEFT));

                // ✅ Pastikan kode hanya 6 digit
                $subdistrictCode = substr($subdistrictCode, 0, 6);

                Subdistrict::updateOrCreate(
                    [
                        'code'          => $subdistrictCode,
                        'province_code' => $provinceCode,
                    ],
                    [
                        'name'          => $formattedName,
                        'district_code' => $districtCode,
                    ]
                );
            }
        }

        $this->command->info("\n✅ SubdistrictSeeder selesai tanpa data ganda.");
    }
}
