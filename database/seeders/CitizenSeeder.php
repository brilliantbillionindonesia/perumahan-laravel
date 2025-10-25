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
                /**
                 * 🔹 Family Card
                 */
                $familyCard = FamilyCard::updateOrCreate(
                    ['family_card_number' => $item['family_card']['family_card_number'] ?? null],
                    [
                        'address'          => $this->formatTitleCase($item['family_card']['address'] ?? null),
                        'rt'               => $item['family_card']['rt'] ?? null,
                        'rw'               => $item['family_card']['rw'] ?? null,
                        'village_code'     => $item['family_card']['village_code'] ?? null,
                        'subdistrict_code' => $item['family_card']['subdistrict_code'] ?? null,
                        'district_code'    => $item['family_card']['district_code'] ?? null,
                        'province_code'    => $item['family_card']['province_code'] ?? null,
                        'postal_code'      => $item['family_card']['postal_code'] ?? null,
                    ]
                );

                $citizenMap = [];
                $citizens = $item['citizens'] ?? [];

                /**
                 * 🔹 Citizens
                 */
                foreach ($citizens as $index => $citizen) {
                    $fullname   = $this->formatTitleCase($citizen['fullname'] ?? '');
                    $birthPlace = $this->formatTitleCase($citizen['birth_place'] ?? '');
                    $birthDate  = $citizen['birth_date'] ?? null;
                    $cardNumber = $citizen['citizen_card_number'] ?? null;

                    // Cari citizen existing
                    $existingCitizen = Citizen::where('fullname', $fullname)
                        ->when($birthDate, fn($q) => $q->where('birth_date', $birthDate))
                        ->first();

                    if (!$existingCitizen && $cardNumber) {
                        $existingCitizen = Citizen::where('citizen_card_number', $cardNumber)->first();
                    }

                    $religion = $this->formatTitleCase($citizen['religion'] ?? '-');
                    $citizenship = 'WNI';

                    // Update atau create citizen
                    if ($existingCitizen) {
                        $existingCitizen->update([
                            'citizen_card_number' => $cardNumber ?? $existingCitizen->citizen_card_number,
                            'family_card_id'      => $familyCard->id,
                            'fullname'            => $fullname,
                            'birth_place'         => $birthPlace ?: $existingCitizen->birth_place,
                            'birth_date'          => $birthDate ?: $existingCitizen->birth_date,
                            'gender'              => $citizen['gender'] ?? $existingCitizen->gender ?? $this->guessGender($fullname),
                            'blood_type'          => $citizen['blood_type'] ?? $existingCitizen->blood_type,
                            'religion'            => $religion,
                            'marital_status'      => $this->formatTitleCase($citizen['marital_status'] ?? $existingCitizen->marital_status),
                            'work_type'           => $this->formatTitleCase($citizen['work_type'] ?? $existingCitizen->work_type),
                            'education_type'      => $this->formatTitleCase($citizen['education_type'] ?? $existingCitizen->education_type),
                            'citizenship'         => $citizenship,
                        ]);
                        $createdCitizen = $existingCitizen;
                    } else {
                        $createdCitizen = Citizen::create([
                            'id'                  => Str::uuid(),
                            'citizen_card_number' => $cardNumber,
                            'family_card_id'      => $familyCard->id,
                            'fullname'            => $fullname,
                            'birth_place'         => $birthPlace,
                            'birth_date'          => $birthDate,
                            'gender'              => $citizen['gender'] ?? $this->guessGender($fullname),
                            'blood_type'          => $citizen['blood_type'] ?? null,
                            'religion'            => $religion,
                            'marital_status'      => $this->formatTitleCase($citizen['marital_status'] ?? null),
                            'work_type'           => $this->formatTitleCase($citizen['work_type'] ?? null),
                            'education_type'      => $this->formatTitleCase($citizen['education_type'] ?? null),
                            'citizenship'         => $citizenship,
                        ]);
                    }

                    $citizenMap[$citizen['id'] ?? $createdCitizen->id] = $createdCitizen->id;

                    /**
                     * 🧩 FamilyMember:
                     *  index 0 => Kepala Keluarga
                     *  index 1 => Istri
                     *  index 2+ => Anak
                     */
                    $relationship = match ($index) {
                        0 => 'Kepala Keluarga',
                        1 => 'Istri',
                        default => 'Anak',
                    };

                    FamilyMember::updateOrCreate(
                        ['citizen_id' => $createdCitizen->id],
                        [
                            'relationship_status' => $relationship,
                            'father_name'         => $this->formatTitleCase($citizen['father_name'] ?? null),
                            'mother_name'         => $this->formatTitleCase($citizen['mother_name'] ?? null),
                        ]
                    );
                }

                /**
                 * 🔹 Kepala Keluarga (index 0)
                 */
                $headCitizenId = $citizens[0]['id'] ?? array_values($citizenMap)[0];

                /**
                 * 🔹 House
                 */
                $house = House::updateOrCreate(
                    [
                        'house_name' => $this->formatTitleCase($item['house']['house_name'] ?? null),
                        'block'      => $this->formatTitleCase($item['house']['block'] ?? null),
                        'number'     => $item['house']['number'] ?? null,
                        'housing_id' => $item['house']['housing_id'] ?? '019a0631-0088-714f-a32c-6eec11ceece9',
                    ],
                    [
                        'family_card_id'  => $familyCard->id,
                        'head_citizen_id' => $headCitizenId,
                    ]
                );

                /**
                 * 🔹 HousingUser
                 */
                foreach ($citizenMap as $citizenId) {
                    HousingUser::updateOrCreate(
                        ['citizen_id' => $citizenId],
                        [
                            'housing_id' => $house->housing_id,
                            'user_id'   => null,
                            'role_code' => 'citizen',
                            'is_active' => 1,
                        ]
                    );
                }
            }
        });
    }

    /**
     * 🔤 Format ke Title Case (contoh: "Mustika Village Karawang")
     */
    private function formatTitleCase(?string $text): ?string
    {
        if (!$text) return null;
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return collect(explode(' ', strtolower($text)))
            ->map(fn($word) => ucfirst($word))
            ->implode(' ');
    }

    private function guessGender(string $fullname): string
    {
        $fullname = strtolower($fullname);
        if (str_contains($fullname, 'binti') || str_contains($fullname, 'siti')) {
            return 'Perempuan';
        }
        return 'Laki-Laki';
    }
}
