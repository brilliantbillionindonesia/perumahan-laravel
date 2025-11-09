<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\FamilyCard;
use App\Models\House;
use App\Models\Citizen;
use App\Models\HousingUser;
use App\Models\FamilyMember;
use App\Models\Housing;

class CitizenSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $path = database_path('data/warga/warga.json');

            if (!file_exists($path)) {
                throw new \Exception("❌ File tidak ditemukan: {$path}");
            }

            $data = json_decode(file_get_contents($path), true);

            foreach ($data as $item) {
                echo "\n🧩 Proses keluarga: {$item['family_card']['family_card_number']}\n";

                // Ambil housing_id dari database (tidak diubah)
                $housing = Housing::first(); // hanya ambil yang sudah ada di DB
                if (!$housing) {
                    throw new \Exception("❌ Tidak ada housing terdaftar di database!");
                }

                // 🟩 1️⃣ Buat Family Card baru
                $familyCard = FamilyCard::create([
                    'id' => Str::uuid(),
                    'family_card_number' => $item['family_card']['family_card_number'] ?? Str::random(10),
                    'address' => $this->formatTitleCase($item['family_card']['address'] ?? 'Alamat Tidak Diketahui'),
                    'rt' => $item['family_card']['rt'] ?? '-',
                    'rw' => $item['family_card']['rw'] ?? '-',
                    'village_code' => $item['family_card']['village_code'] ?? null,
                    'subdistrict_code' => $item['family_card']['subdistrict_code'] ?? null,
                    'district_code' => $item['family_card']['district_code'] ?? null,
                    'province_code' => $item['family_card']['province_code'] ?? null,
                    'postal_code' => $item['family_card']['postal_code'] ?? null,
                ]);

                // 🟨 2️⃣ Buat Citizen per anggota keluarga
                $citizens = $item['citizens'] ?? [];
                $citizenMap = [];
                $fatherName = null;
                $motherName = null;

                foreach ($citizens as $index => $citizen) {
                    $fullname = $this->formatTitleCase($citizen['fullname'] ?? 'Tanpa Nama');
                    $relationship = match ($index) {
                        0 => 'Kepala Keluarga',
                        1 => 'Istri',
                        default => 'Anak',
                    };

                    $citizenModel = Citizen::create([
                        'id' => Str::uuid(),
                        'family_card_id' => $familyCard->id,
                        'citizen_card_number' => $citizen['citizen_card_number'] ?? null,
                        'fullname' => $fullname,
                        'birth_place' => $this->formatTitleCase($citizen['birth_place'] ?? '-'),
                        'birth_date' => $citizen['birth_date'] ?? now()->subYears(rand(1, 60)),
                        'gender' => $citizen['gender'] ?? $this->guessGender($fullname),
                        'blood_type' => $citizen['blood_type'] ?? null,
                        'religion' => $this->formatTitleCase($citizen['religion'] ?? 'Islam'),
                        'marital_status' => $this->formatTitleCase($citizen['marital_status'] ?? '-'),
                        'work_type' => $this->formatTitleCase($citizen['work_type'] ?? 'Tidak Bekerja'),
                        'education_type' => $this->formatTitleCase($citizen['education_type'] ?? '-'),
                        'citizenship' => 'WNI',
                    ]);

                    $citizenMap[(string) $citizenModel->id] = $relationship;

                    if ($relationship === 'Kepala Keluarga') {
                        $fatherName = $fullname;
                    } elseif ($relationship === 'Istri') {
                        $motherName = $fullname;
                    }

                    // 🧩 3️⃣ Buat Family Member
                    FamilyMember::create([
                        'id' => Str::uuid(),
                        'citizen_id' => $citizenModel->id,
                        'relationship_status' => $relationship,
                        'father_name' => $relationship === 'Anak' ? $fatherName : null,
                        'mother_name' => $relationship === 'Anak' ? $motherName : null,
                        'is_ai_generated' => false,
                    ]);
                }

                // 🏠 4️⃣ Buat House (menggunakan housing_id dari DB)
                $house = House::create([
                    'id' => Str::uuid(),
                    'housing_id' => $housing->id,
                    'house_name' => $this->formatTitleCase($item['house']['house_name'] ?? $housing->housing_name),
                    'block' => $this->formatTitleCase($item['house']['block'] ?? 'A'),
                    'number' => $item['house']['number'] ?? rand(1, 200),
                    'family_card_id' => $familyCard->id,
                    'head_citizen_id' => (string) array_key_first($citizenMap),
                ]);

                // 🧱 5️⃣ Tambah ke Housing User
                foreach ($citizenMap as $citizenId => $rel) {
                    HousingUser::create([
                        'id' => Str::uuid(),
                        'housing_id' => $housing->id,
                        'citizen_id' => $citizenId,
                        'is_active' => 1,
                        'role_code' => 'citizen',
                    ]);
                }

                echo "✅ Data keluarga berhasil dibuat: {$familyCard->family_card_number}\n";
            }
        });
    }

    private function formatTitleCase(?string $text): ?string
    {
        if (!$text) return null;
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return collect(explode(' ', strtolower($text)))
            ->map(fn($word) => ucfirst($word))
            ->implode(' ');
    }
}
