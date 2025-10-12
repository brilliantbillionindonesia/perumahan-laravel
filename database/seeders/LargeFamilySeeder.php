<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

use App\Models\User;
use App\Models\Housing;
use App\Models\HousingUser;
use App\Models\FamilyCard;
use App\Models\FamilyMember;
use App\Models\Citizen;
use App\Models\House;

use App\Constants\GenderOption;
use App\Constants\BloodTypeOption;
use App\Constants\ReligionOption;
use App\Constants\MaritalStatusOption;
use App\Constants\WorkTypeOption;
use App\Constants\EducationTypeOption;
use App\Constants\CitizenshipOption;
use App\Constants\RelationshipStatusOption;

class LargeFamilySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $faker = Faker::create('id_ID');

            // helper: bikin email unik dari nama
            $emailFromName = function (string $name): string {
                $base = Str::of($name)->ascii()->lower()
                    ->replaceMatches('/[^a-z0-9]+/', '.')
                    ->trim('.')
                    ->toString();
                $email = $base.'@mail.com';
                $i = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $base."+{$i}@mail.com";
                    $i++;
                }
                return $email;
            };

            // ====== buat 10 perumahan ======
            $housingIds = [];
            for ($i = 1; $i <= 10; $i++) {
                $h = Housing::firstOrCreate(
                    ['housing_name' => "Perumahan Nusantara $i"],
                    [
                        'address' => "Jl. Nusantara $i",
                        'rt' => str_pad((string) $faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
                        'rw' => str_pad((string) $faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
                        'village_code' => '1101012001',
                        'subdistrict_code' => '110101',
                        'district_code' => '1101',
                        'province_code' => '11',
                        'postal_code' => (string) $faker->numberBetween(41111, 41999),
                    ]
                );
                $housingIds[] = $h->id;
            }

            // ====== generate warga (1000) + buat akun user dari nama ======
            $targetCitizens = 1000;
            $madeCitizens   = 0;
            $kkCounter      = 0;

            while ($madeCitizens < $targetCitizens) {
                $kkCounter++;
                $hid = $faker->randomElement($housingIds);

                $familyCard = FamilyCard::create([
                    'family_card_number' => $faker->unique()->numerify('##########').$faker->numerify('######'),
                    'address' => $faker->streetAddress(),
                    'rt' => str_pad((string) $faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
                    'rw' => str_pad((string) $faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
                    'village_code' => '1101012001',
                    'subdistrict_code' => '110101',
                    'district_code' => '1101',
                    'province_code' => '11',
                    'postal_code' => (string) $faker->numberBetween(41111, 41999),
                ]);

                $remain   = $targetCitizens - $madeCitizens;
                $membersN = max(1, min($remain, $faker->numberBetween(3, 4)));

                $rolesPool = [RelationshipStatusOption::KEPALA_KELUARGA];
                if ($membersN >= 2) $rolesPool[] = RelationshipStatusOption::ISTRI;
                for ($x = count($rolesPool); $x < $membersN; $x++) $rolesPool[] = RelationshipStatusOption::ANAK;

                $headCitizenId = null;

                for ($m = 0; $m < $membersN; $m++) {
                    $gender   = $faker->randomElement([GenderOption::LAKILAKI, GenderOption::PEREMPUAN]);
                    $fullname = $gender === GenderOption::LAKILAKI ? $faker->name('male') : $faker->name('female');

                    $citizen = Citizen::create([
                        'family_card_id'      => $familyCard->id,
                        'citizen_card_number' => $faker->unique()->numerify('##########').$faker->numerify('######'),
                        'fullname'            => $fullname,
                        'gender'              => $gender,
                        'birth_place'         => $faker->city(),
                        'birth_date'          => $faker->date('Y-m-d', '-18 years'),
                        'blood_type'          => $faker->randomElement([BloodTypeOption::A, BloodTypeOption::B, BloodTypeOption::AB, BloodTypeOption::O]),
                        'religion'            => $faker->randomElement([ReligionOption::ISLAM, ReligionOption::KRISTEN, ReligionOption::KATOLIK, ReligionOption::BUDDHA, ReligionOption::HINDU, ReligionOption::KONGHUCU]),
                        'marital_status'      => $faker->randomElement([MaritalStatusOption::KAWIN, MaritalStatusOption::BELUMKAWIN]),
                        'work_type'           => $faker->randomElement([WorkTypeOption::PNS, WorkTypeOption::KARYAWAN, WorkTypeOption::WIRASWASTA, WorkTypeOption::GURU, WorkTypeOption::PETANI, WorkTypeOption::IBU_RUMAH_TANGGA]),
                        'education_type'      => $faker->randomElement([EducationTypeOption::SD, EducationTypeOption::SMP, EducationTypeOption::SMA, EducationTypeOption::DIPLOMA_1, EducationTypeOption::SARJANA, EducationTypeOption::MAGISTER]),
                        'citizenship'         => CitizenshipOption::WNI,
                        'death_certificate_id'=> null,
                    ]);

                    FamilyMember::create([
                        'citizen_id'          => $citizen->id,
                        'relationship_status' => $rolesPool[$m],
                    ]);

                    if ($rolesPool[$m] === RelationshipStatusOption::KEPALA_KELUARGA) {
                        $headCitizenId = $citizen->id;
                    }

                    // === buat user utk setiap citizen ===
                    $email = $emailFromName($fullname);
                    $user  = User::firstOrCreate(
                        ['email' => $email],
                        ['name' => $fullname, 'password' => Hash::make('12345678')]
                    );

                    HousingUser::firstOrCreate([
                        'housing_id' => $hid,
                        'user_id'    => $user->id,
                        'citizen_id' => $citizen->id,
                        'role_code'  => 'citizen',
                    ]);

                    $madeCitizens++;
                    if ($madeCitizens >= $targetCitizens) break 2;
                }

                House::create([
                    'housing_id'      => $hid,
                    'family_card_id'  => $familyCard->id,
                    'house_name'      => "Rumah Keluarga #$kkCounter",
                    'block'           => 'A'.$faker->numberBetween(1, 9),
                    'number'          => $faker->numberBetween(1, 120),
                    'head_citizen_id' => $headCitizenId,
                ]);
            }

            // ====== staff per housing (admin, sekretaris, bendahara, manager, security) ======
            $staff = [
                ['code' => 'admin',      'name' => 'Admin'],
                ['code' => 'sekretaris', 'name' => 'Sekretaris'],
                ['code' => 'bendahara',  'name' => 'Bendahara'],
                ['code' => 'manager',    'name' => 'Manager'],
                ['code' => 'security',   'name' => 'Satpam'],
            ];

            foreach ($housingIds as $idx => $hid) {
                foreach ($staff as $st) {
                    // email unik: namarole{idx}@mail.com
                    $email = "{$st['code']}{$idx}@mail.com";
                    $user  = User::firstOrCreate(
                        ['email' => $email],
                        ['name' => $st['name'].' '.$idx, 'password' => Hash::make('12345678')]
                    );

                    HousingUser::firstOrCreate([
                        'housing_id' => $hid,
                        'user_id'    => $user->id,
                        'citizen_id' => null,
                        'role_code'  => $st['code'],
                    ]);
                }
            }
        });
    }
}
