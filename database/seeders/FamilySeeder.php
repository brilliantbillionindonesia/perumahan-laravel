<?php

namespace Database\Seeders;

use App\Constants\RelationshipStatusOption;
use App\Models\FamilyMember;
use App\Models\House;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Housing;
use App\Models\HousingUser;
use App\Models\FamilyCard;
use App\Models\Citizen;

use App\Constants\GenderOption;
use App\Constants\BloodTypeOption;
use App\Constants\ReligionOption;
use App\Constants\MaritalStatusOption;
use App\Constants\WorkTypeOption;
use App\Constants\EducationTypeOption;
use App\Constants\CitizenshipOption;
use Schema;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // ===== Seed 2 user awal =====
            $createdUser = User::firstOrCreate(
                ['email' => 'admin-mustika@mail.com'],
                ['name' => 'Admin Mustika', 'password' => Hash::make('password')]
            );

            $createdUser2 = User::firstOrCreate(
                ['email' => 'admin-grisen@mail.com'],
                ['name' => 'Admin Grisent', 'password' => Hash::make('password')]
            );

            // ===== Seed 3 housing =====
            $createdHousing = Housing::firstOrCreate([
                'housing_name' => 'Mustika Village Karawang',
                'address' => 'Jl. Mustika Karawang',
                'rt' => '01',
                'rw' => '01',
                'village_code' => '1101012001',
                'subdistrict_code' => '110101',
                'district_code' => '1101',
                'province_code' => '11',
                'postal_code' => '41352',
            ]);

            $createdHousing2 = Housing::firstOrCreate([
                'housing_name' => 'Griya Sentosa',
                'address' => 'Jl. Griya Sentosa',
                'rt' => '02',
                'rw' => '02',
                'village_code' => '1101012001',
                'subdistrict_code' => '110101',
                'district_code' => '1101',
                'province_code' => '11',
                'postal_code' => '41352',
            ]);

            $createdHousing3 = Housing::firstOrCreate([
                'housing_name' => 'Brilliant Appartment',
                'address' => 'Jl. Brilliant Billion Indonesia',
                'rt' => '03',
                'rw' => '03',
                'village_code' => '1101012001',
                'subdistrict_code' => '110101',
                'district_code' => '1101',
                'province_code' => '11',
                'postal_code' => '41352',
            ]);

            // Relasi user awal -> housing (contoh)
            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing->id,
                'user_id' => $createdUser->id,
                'citizen_id' => null,
                'role_code' => 'admin',
            ]);

            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing2->id,
                'user_id' => $createdUser->id,
                'citizen_id' => null,
                'role_code' => 'citizen',
            ]);

            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing->id,
                'user_id' => $createdUser2->id,
                'citizen_id' => null,
                'role_code' => 'citizen',
            ]);

            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing2->id,
                'user_id' => $createdUser2->id,
                'citizen_id' => null,
                'role_code' => 'admin',
            ]);

            // ===== Data 3 FamilyCard + 2 Citizen per kartu =====
            $familyCards = [
                [
                    'family_card_number' => '1234567890113455',
                    'address' => 'Jl. Mustika Karawang',
                    'rt' => '01',
                    'rw' => '01',
                    'village_code' => '1101012001',
                    'subdistrict_code' => '110101',
                    'district_code' => '1101',
                    'province_code' => '11',
                    'postal_code' => '41352',
                    'housing_id' => [
                        $createdHousing->id,
                        $createdHousing3->id,
                    ],
                    'houses' => [
                        [
                            'housing_id' => $createdHousing->id,
                            'house_name' => 'Rumah pak Mustika',
                            'block' => 'A2',
                            'number' => 10,
                        ],
                        [
                            'housing_id' => $createdHousing3->id,
                            'house_name' => 'Rumah pak Mustika',
                            'block' => 'A4',
                            'number' => 30,
                        ]
                    ],
                    'citizens' => [
                        [
                            'citizen_card_number' => '1234567890113456',
                            'fullname' => 'Pak Mustika',
                            'gender' => GenderOption::LAKILAKI,
                            'birth_place' => 'Jakarta',
                            'birth_date' => '2000-01-01',
                            'blood_type' => BloodTypeOption::A,
                            'religion' => ReligionOption::ISLAM,
                            'marital_status' => MaritalStatusOption::KAWIN,
                            'work_type' => WorkTypeOption::PNS,
                            'education_type' => EducationTypeOption::SMP,
                            'citizenship' => CitizenshipOption::WNI,
                            'death_certificate_id' => null,
                            'user' => [
                                'name' => 'Si Kepala',
                                'email' => 'si-kepala@mail.com',
                                'password' => 'password',
                            ],
                            'relationship_status' => RelationshipStatusOption::KEPALA_KELUARGA
                        ],
                        [
                            'citizen_card_number' => '1234567890113457',
                            'fullname' => 'Ibu Mustika',
                            'gender' => GenderOption::PEREMPUAN,
                            'birth_place' => 'Jakarta',
                            'birth_date' => '2000-01-01',
                            'blood_type' => BloodTypeOption::A,
                            'religion' => ReligionOption::ISLAM,
                            'marital_status' => MaritalStatusOption::KAWIN,
                            'work_type' => WorkTypeOption::PNS,
                            'education_type' => EducationTypeOption::SMP,
                            'citizenship' => CitizenshipOption::WNI,
                            'death_certificate_id' => null,
                            'user' => [], // tanpa akun user.
                            'relationship_status' => RelationshipStatusOption::ISTRI
                        ],
                    ],
                ],
                [
                    'family_card_number' => '2234567890113455',
                    'address' => 'Jl. Satria Piningit Raya',
                    'rt' => '02',
                    'rw' => '02',
                    'village_code' => '1101012001',
                    'subdistrict_code' => '110101',
                    'district_code' => '1101',
                    'province_code' => '11',
                    'postal_code' => '41352',
                    'housing_id' => [
                        $createdHousing->id,
                        $createdHousing2->id,
                        $createdHousing3->id,
                    ],
                    'houses' => [
                        [
                            'housing_id' => $createdHousing->id,
                            'house_name' => 'Rumah pak Satria Piningit',
                            'block' => 'A1',
                            'number' => 10,
                        ],
                        [
                            'housing_id' => $createdHousing2->id,
                            'house_name' => 'Rumah pak Satria Piningit',
                            'block' => 'A2',
                            'number' => 20,
                        ],
                        [
                            'housing_id' => $createdHousing3->id,
                            'house_name' => 'Rumah pak Satria Piningit',
                            'block' => 'A3',
                            'number' => 30,
                        ]
                    ],
                    'citizens' => [
                        [
                            'citizen_card_number' => '2234567890113456',
                            'fullname' => 'Bapak Satria Piningit',
                            'gender' => GenderOption::LAKILAKI,
                            'birth_place' => 'Karawang',
                            'birth_date' => '1990-02-02',
                            'blood_type' => BloodTypeOption::B,
                            'religion' => ReligionOption::ISLAM,
                            'marital_status' => MaritalStatusOption::KAWIN,
                            'work_type' => WorkTypeOption::PETANI,
                            'education_type' => EducationTypeOption::SMA,
                            'citizenship' => CitizenshipOption::WNI,
                            'death_certificate_id' => null,
                            'user' => [
                                'name' => 'Bapak Satria Piningit',
                                'email' => 'satria-piningit@mail.com',
                                'password' => 'password',
                            ],
                            'relationship_status' => RelationshipStatusOption::KEPALA_KELUARGA
                        ],
                        [
                            'citizen_card_number' => '2234567890113457',
                            'fullname' => 'Ibu Satria Piningit',
                            'gender' => GenderOption::PEREMPUAN,
                            'birth_place' => 'Karawang',
                            'birth_date' => '1992-03-03',
                            'blood_type' => BloodTypeOption::B,
                            'religion' => ReligionOption::ISLAM,
                            'marital_status' => MaritalStatusOption::KAWIN,
                            'work_type' => WorkTypeOption::IBU_RUMAH_TANGGA,
                            'education_type' => EducationTypeOption::SMA,
                            'citizenship' => CitizenshipOption::WNI,
                            'death_certificate_id' => null,
                            'user' => [],
                            'relationship_status' => RelationshipStatusOption::ISTRI
                        ],
                    ],
                ],
                [
                    'family_card_number' => '3234567890113455',
                    'address' => 'Jl. Sudirman Indah',
                    'rt' => '03',
                    'rw' => '03',
                    'village_code' => '1101012001',
                    'subdistrict_code' => '110101',
                    'district_code' => '1101',
                    'province_code' => '11',
                    'postal_code' => '41352',
                    'housing_id' => [
                        $createdHousing->id,
                        $createdHousing2->id,
                        $createdHousing3->id,
                    ],
                    'houses' => [
                        [
                            'housing_id' => $createdHousing->id,
                            'house_name' => 'Rumah pak Sudirman',
                            'block' => 'A1',
                            'number' => 1,
                        ],
                        [
                            'housing_id' => $createdHousing2->id,
                            'house_name' => 'Rumah pak Sudirman',
                            'block' => 'A2',
                            'number' => 2,
                        ],
                        [
                            'housing_id' => $createdHousing3->id,
                            'house_name' => 'Rumah pak Sudirman',
                            'block' => 'A3',
                            'number' => 3,
                        ]
                    ],
                    'citizens' => [
                        [
                            'citizen_card_number' => '3234567890113456',
                            'fullname' => 'Pak Sudirman',
                            'gender' => GenderOption::LAKILAKI,
                            'birth_place' => 'Bandung',
                            'birth_date' => '1985-05-05',
                            'blood_type' => BloodTypeOption::O,
                            'religion' => ReligionOption::KRISTEN,
                            'marital_status' => MaritalStatusOption::KAWIN,
                            'work_type' => WorkTypeOption::KARYAWAN, // sesuaikan dg konstanta milikmu
                            'education_type' => EducationTypeOption::SARJANA,
                            'citizenship' => CitizenshipOption::WNI,
                            'death_certificate_id' => null,
                            'user' => [],
                            'relationship_status' => RelationshipStatusOption::KEPALA_KELUARGA
                        ],
                        [
                            'citizen_card_number' => '3234567890113457',
                            'fullname' => 'Bu Sudirman',
                            'gender' => GenderOption::PEREMPUAN,
                            'birth_place' => 'Bandung',
                            'birth_date' => '1987-06-06',
                            'blood_type' => BloodTypeOption::O,
                            'religion' => ReligionOption::KRISTEN,
                            'marital_status' => MaritalStatusOption::KAWIN,
                            'work_type' => WorkTypeOption::GURU,
                            'education_type' => EducationTypeOption::SARJANA,
                            'citizenship' => CitizenshipOption::WNI,
                            'death_certificate_id' => null,
                            'user' => [],
                            'relationship_status' => RelationshipStatusOption::ISTRI
                        ],
                    ],
                ],
            ];
            foreach ($familyCards as $fcData) {
                $citizensData = $fcData['citizens'] ?? [];
                $housingIds = $fcData['housing_id'] ?? [];
                $housesData = $fcData['houses'] ?? [];

                unset($fcData['citizens'], $fcData['housing_id'], $fcData['houses']);

                // Buat Family Card
                $familyCard = FamilyCard::create($fcData);

                // ===== Buat CITIZENS lebih dulu =====
                $headCitizenId = null;
                $citizenIds = [];

                foreach ($citizensData as $citizenData) {
                    $relationship = $citizenData['relationship_status']
                        ?? RelationshipStatusOption::LAINNYA;
                    unset($citizenData['relationship_status']);

                    $userPayload = $citizenData['user'] ?? [];
                    unset($citizenData['user']);

                    $citizenData['family_card_id'] = $familyCard->id;
                    $citizen = Citizen::create($citizenData);

                    // Simpan family member
                    FamilyMember::create([
                        'citizen_id' => $citizen->id,
                        'relationship_status' => $relationship,
                    ]);

                    // Cek apakah kepala keluarga
                    if ($relationship === RelationshipStatusOption::KEPALA_KELUARGA) {
                        $headCitizenId = $citizen->id;
                    }

                    // Buat user jika ada payload
                    $userId = null;
                    if (!empty($userPayload) && !empty($userPayload['email'])) {
                        $user = User::firstOrCreate(
                            ['email' => $userPayload['email']],
                            [
                                'name' => $userPayload['name'] ?? $citizen->fullname,
                                'password' => Hash::make($userPayload['password'] ?? 'password'),
                            ]
                        );
                        $userId = $user->id;
                    }

                    foreach ($housingIds as $hid) {
                        HousingUser::firstOrCreate([
                            'housing_id' => $hid,
                            'user_id' => $userId,
                            'citizen_id' => $citizen->id,
                            'role_code' => 'citizen',
                        ]);
                    }
                }

                // ===== Baru buat HOUSES dengan head_citizen_id =====
                foreach ($housesData as $house) {
                    House::create([
                        'housing_id' => $house['housing_id'],
                        'family_card_id' => $familyCard->id,
                        'house_name' => $house['house_name'] ?? null,
                        'block' => $house['block'] ?? null,
                        'number' => $house['number'] ?? null,
                        'head_citizen_id' => $headCitizenId,   // <--- otomatis kepala keluarga
                    ]);
                }
            }
        });
    }
}
