<?php

namespace Database\Seeders;

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
                'housing_name'   => 'Mustika Village Karawang',
                'address'        => 'Jl. Mustika Karawang',
                'rt'             => '01',
                'rw'             => '01',
                'village_code'   => '01',
                'subdistrict_code'=> '01',
                'district_code'  => '01',
                'province_code'  => '01',
                'postal_code'    => '41351',
            ]);

            $createdHousing2 = Housing::firstOrCreate([
                'housing_name'   => 'Griya Sentosa',
                'address'        => 'Jl. Griya Sentosa',
                'rt'             => '01',
                'rw'             => '01',
                'village_code'   => '01',
                'subdistrict_code'=> '01',
                'district_code'  => '01',
                'province_code'  => '01',
                'postal_code'    => '41352',
            ]);

            $createdHousing3 = Housing::firstOrCreate([
                'housing_name'   => 'Brilliant Appartment',
                'address'        => 'Jl. Brilliant Billion Indonesia',
                'rt'             => '01',
                'rw'             => '01',
                'village_code'   => '01',
                'subdistrict_code'=> '01',
                'district_code'  => '01',
                'province_code'  => '01',
                'postal_code'    => '41352',
            ]);

            // Relasi user awal -> housing (contoh)
            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing->id,
                'user_id'    => $createdUser->id,
                'citizen_id' => null,
                'role_code'  => 'admin',
            ]);

            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing2->id,
                'user_id'    => $createdUser->id,
                'citizen_id' => null,
                'role_code'  => 'citizen',
            ]);

            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing->id,
                'user_id'    => $createdUser2->id,
                'citizen_id' => null,
                'role_code'  => 'citizen',
            ]);

            HousingUser::firstOrCreate([
                'housing_id' => $createdHousing2->id,
                'user_id'    => $createdUser2->id,
                'citizen_id' => null,
                'role_code'  => 'admin',
            ]);

            // ===== Data 3 FamilyCard + 2 Citizen per kartu =====
            $familyCards = [
                [
                    'family_card_number' => '1234567890113455',
                    'address'            => 'Jl. Mustika Karawang',
                    'rt'                 => '01',
                    'rw'                 => '01',
                    'village_code'       => '01',
                    'subdistrict_code'   => '01',
                    'district_code'      => '01',
                    'province_code'      => '01',
                    'postal_code'        => '41352',
                    'housing_id'         => [
                        $createdHousing->id,
                        $createdHousing3->id,
                    ],
                    'citizens'           => [
                        [
                            'citizen_card_number' => '1234567890113456',
                            'fullname'            => 'Si Kepala',
                            'gender'              => GenderOption::LAKILAKI,
                            'birth_place'         => 'Jakarta',
                            'birth_date'          => '2000-01-01',
                            'blood_type'          => BloodTypeOption::A,
                            'religion'            => ReligionOption::ISLAM,
                            'marital_status'      => MaritalStatusOption::KAWIN,
                            'work_type'           => WorkTypeOption::PNS,
                            'education_type'      => EducationTypeOption::SMP,
                            'citizenship'         => CitizenshipOption::WNI,
                            'death_certificate_id'=> null,
                            'user'                => [
                                'name'     => 'Si Kepala',
                                'email'    => 'si-kepala@mail.com',
                                'password' => 'password',
                            ],
                        ],
                        [
                            'citizen_card_number' => '1234567890113457',
                            'fullname'            => 'Si Ibu',
                            'gender'              => GenderOption::PEREMPUAN,
                            'birth_place'         => 'Jakarta',
                            'birth_date'          => '2000-01-01',
                            'blood_type'          => BloodTypeOption::A,
                            'religion'            => ReligionOption::ISLAM,
                            'marital_status'      => MaritalStatusOption::KAWIN,
                            'work_type'           => WorkTypeOption::PNS,
                            'education_type'      => EducationTypeOption::SMP,
                            'citizenship'         => CitizenshipOption::WNI,
                            'death_certificate_id'=> null,
                            'user'                => [], // tanpa akun user
                        ],
                    ],
                ],
                [
                    'family_card_number' => '2234567890113455',
                    'address'            => 'Jl. Melati Raya',
                    'rt'                 => '02',
                    'rw'                 => '02',
                    'village_code'       => '02',
                    'subdistrict_code'   => '02',
                    'district_code'      => '02',
                    'province_code'      => '01',
                    'postal_code'        => '41353',
                    'housing_id'         => [
                        $createdHousing->id,
                        $createdHousing2->id,
                        $createdHousing3->id,
                    ],
                    'citizens'           => [
                        [
                            'citizen_card_number' => '2234567890113456',
                            'fullname'            => 'Bapak Melati',
                            'gender'              => GenderOption::LAKILAKI,
                            'birth_place'         => 'Karawang',
                            'birth_date'          => '1990-02-02',
                            'blood_type'          => BloodTypeOption::B,
                            'religion'            => ReligionOption::ISLAM,
                            'marital_status'      => MaritalStatusOption::KAWIN,
                            'work_type'           => WorkTypeOption::PETANI,
                            'education_type'      => EducationTypeOption::SMA,
                            'citizenship'         => CitizenshipOption::WNI,
                            'death_certificate_id'=> null,
                            'user'                => [
                                'name'     => 'Bapak Melati',
                                'email'    => 'bapak-melati@mail.com',
                                'password' => 'password',
                            ],
                        ],
                        [
                            'citizen_card_number' => '2234567890113457',
                            'fullname'            => 'Ibu Melati',
                            'gender'              => GenderOption::PEREMPUAN,
                            'birth_place'         => 'Karawang',
                            'birth_date'          => '1992-03-03',
                            'blood_type'          => BloodTypeOption::B,
                            'religion'            => ReligionOption::ISLAM,
                            'marital_status'      => MaritalStatusOption::KAWIN,
                            'work_type'           => WorkTypeOption::IBU_RUMAH_TANGGA,
                            'education_type'      => EducationTypeOption::SMA,
                            'citizenship'         => CitizenshipOption::WNI,
                            'death_certificate_id'=> null,
                            'user'                => [],
                        ],
                    ],
                ],
                [
                    'family_card_number' => '3234567890113455',
                    'address'            => 'Jl. Kenanga Indah',
                    'rt'                 => '03',
                    'rw'                 => '03',
                    'village_code'       => '03',
                    'subdistrict_code'   => '03',
                    'district_code'      => '03',
                    'province_code'      => '01',
                    'postal_code'        => '41354',
                    'housing_id'         => [
                        $createdHousing->id,
                        $createdHousing2->id,
                        $createdHousing3->id,
                    ],
                    'citizens'           => [
                        [
                            'citizen_card_number' => '3234567890113456',
                            'fullname'            => 'Pak Kenanga',
                            'gender'              => GenderOption::LAKILAKI,
                            'birth_place'         => 'Bandung',
                            'birth_date'          => '1985-05-05',
                            'blood_type'          => BloodTypeOption::O,
                            'religion'            => ReligionOption::KRISTEN,
                            'marital_status'      => MaritalStatusOption::KAWIN,
                            'work_type'           => WorkTypeOption::KARYAWAN, // sesuaikan dg konstanta milikmu
                            'education_type'      => EducationTypeOption::SARJANA,
                            'citizenship'         => CitizenshipOption::WNI,
                            'death_certificate_id'=> null,
                            'user'                => [],
                        ],
                        [
                            'citizen_card_number' => '3234567890113457',
                            'fullname'            => 'Bu Kenanga',
                            'gender'              => GenderOption::PEREMPUAN,
                            'birth_place'         => 'Bandung',
                            'birth_date'          => '1987-06-06',
                            'blood_type'          => BloodTypeOption::O,
                            'religion'            => ReligionOption::KRISTEN,
                            'marital_status'      => MaritalStatusOption::KAWIN,
                            'work_type'           => WorkTypeOption::GURU,
                            'education_type'      => EducationTypeOption::SARJANA,
                            'citizenship'         => CitizenshipOption::WNI,
                            'death_certificate_id'=> null,
                            'user'                => [],
                        ],
                    ],
                ],
            ];

            // ===== Insert FamilyCards & Citizens (+ optional Users & HousingUsers) =====
            foreach ($familyCards as $fcData) {
                $citizensData = $fcData['citizens'] ?? [];
                $housingIds   = $fcData['housing_id'] ?? [];

                // buang key non-kolom sebelum create
                unset($fcData['citizens'], $fcData['housing_id']);

                $familyCard = FamilyCard::create($fcData);

                foreach ($citizensData as $citizenData) {
                    $userPayload = $citizenData['user'] ?? [];
                    unset($citizenData['user']);

                    $citizenData['family_card_id'] = $familyCard->id;
                    $citizen = Citizen::create($citizenData);

                    // Jika user disediakan, buat user lalu tautkan ke semua housing ids sebagai citizen
                    $userId = null;
                    if (!empty($userPayload) && !empty($userPayload['email'])) {
                        $user = User::firstOrCreate(
                            ['email' => $userPayload['email']],
                            [
                                'name'     => $userPayload['name'] ?? $citizen->fullname,
                                'password' => Hash::make($userPayload['password'] ?? 'password'),
                            ]
                        );
                        $userId = $user->id;
                    }

                    // Buat HousingUser untuk setiap housing_id yang ditentukan
                    foreach ($housingIds as $hid) {
                        HousingUser::firstOrCreate([
                            'housing_id' => $hid,
                            'user_id'    => $userId,         // bisa null jika citizen tidak punya akun user
                            'citizen_id' => $citizen->id,    // hubungkan citizen-nya
                            'role_code'  => 'citizen',       // default; ubah sesuai kebutuhanmu
                        ]);
                    }
                }
            }
        });
    }
}
