<?php

namespace App\Http\Controllers\Web\Management;

use App\Constants\BloodTypeOption;
use App\Constants\EducationTypeOption;
use App\Constants\GenderOption;
use App\Constants\HttpStatusCodes;
use App\Constants\MaritalStatusOption;
use App\Constants\RelationshipStatusOption;
use App\Constants\ReligionOption;
use App\Constants\WorkTypeOption;
use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\FamilyMember;
use App\Models\House;
use App\Models\HousingUser;
use Carbon\Carbon;
use Date;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Storage;
use Validator;


class CitizenController extends Controller
{
    public function import(Request $request)
    {

        Citizen::truncate();
        FamilyCard::truncate();
        FamilyMember::truncate();
        House::truncate();
        HousingUser::truncate();
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'housing_id' => 'required|exists:housings,id',
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        $housing_id = $request->input('housing_id');
        $name = 'citizen-import-' . $housing_id . '-' . time() . '.xlsx';
        $path = $file->storeAs('file_import', $name, 'local');
        $fullPath = Storage::disk('local')->path($path);
        if (!file_exists($fullPath)) {
            throw new \Exception("File tidak ditemukan: " . $fullPath);
        }
        $spreadsheet = IOFactory::load($fullPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        foreach ($sheetData as $key => $value) {

            if ($key == 0) {
                continue;
            }

            if (trim($value[3]) == "") {
                continue;
            }

            $fullname = ucwords(strtolower($value[3]));
            $cekCitizen = Citizen::where('fullname', $fullname)->first();

            if ($cekCitizen) {
                continue;
            }

            DB::transaction(function () use ($value, $fullname, $housing_id) {

                $citizen = new Citizen();
                $citizen->citizen_card_number = $value[2] != "" ? trim($value[2]) : null;
                $citizen->fullname = $fullname;
                $citizen->gender = GenderOption::getTypeOption($value[14]);
                $citizen->birth_place = $value[12] != "" ? ucwords($value[12]) : null;
                $citizen->birth_date = $this->normalizeExcelDate($value[8]);
                $citizen->blood_type = BloodTypeOption::NA;
                $citizen->religion = ReligionOption::getTypeOption($value[15]);
                $citizen->marital_status = MaritalStatusOption::getTypeOption($value[16]);
                $citizen->work_type = WorkTypeOption::getTypeOption($value[17]);
                $citizen->education_type = EducationTypeOption::getTypeOption($value[18]);
                $citizen->citizenship = 'WNA';
                $citizen->save();

                $familyMember = new FamilyMember();
                $familyMember->citizen_id = $citizen->id;
                $familyMember->relationship_status = $value[7];
                $familyMember->father_name = $value[19] != "" ? ucwords(strtolower($value[19])) : null;
                $familyMember->mother_name = $value[19] != "" ? ucwords(strtolower($value[21])) : null;
                $familyMember->save();

                $block = str_replace("-", "", strtoupper($value[4]));
                $number = (int) $value[5];

                $cekHouse = House::where('block', $block)
                    ->where('number', $number)
                    ->first();

                $familyCard = null;
                if ($value[7] == RelationshipStatusOption::KEPALA_KELUARGA) {

                    if ($cekHouse == null) {
                        $familyCard = new FamilyCard();
                        $familyCard->family_card_number = $value[1] != "" ? trim($value[1]) : null;
                        $familyCard->address = $value[6] != "" ? ucwords(strtolower($value[6] ?? '')) : null;
                        $familyCard->save();

                        $house = new House();
                        $house->housing_id = $housing_id;
                        $house->house_name = 'Rumah ' . $block . '-' . $number;
                        $house->block = $block;
                        $house->number = $number;
                        $house->family_card_id = $familyCard->id;
                        $house->head_citizen_id = $citizen->id;
                        $house->save();
                    } else {
                        $house = $cekHouse;
                        $house->head_citizen_id = $citizen->id;
                        $house->save();
                    }
                }

                $familyCardId = null;
                if ($familyCard != null) {
                    $familyCardId = $familyCard->id;
                } else {
                    if ($cekHouse) {
                        $familyCardId = $cekHouse->family_card_id;
                    }
                }

                $citizen->family_card_id = $familyCardId;
                $citizen->save();

                $housingUser = new HousingUser();
                $housingUser->housing_id = $housing_id;
                $housingUser->citizen_id = $citizen->id;
                $housingUser->role_code = "citizen";
                $housingUser->is_active = 1;
                $housingUser->save();

            });
        }
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_CREATED,
            'message' => "Import data warga berhasil",
        ], HttpStatusCodes::HTTP_CREATED);
    }

    function normalizeExcelDate($value)
    {
        // Jika kosong, return null
        if (empty($value))
            return null;

        // 1️⃣ Jika numeric → Excel serial date
        if (is_numeric($value)) {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
        }

        // 2️⃣ Jika format Jepang seperti 1986年12月28日
        if (preg_match('/(\d{4})年(\d{1,2})月(\d{1,2})日/', $value, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
        }

        // 3️⃣ Jika format Eropa/Indonesia 28/12/1986
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/', $value, $matches)) {
            // Tangani kalau tahun 2 digit (misal 86)
            $year = (strlen($matches[3]) == 2) ? '19' . $matches[3] : $matches[3];
            return sprintf('%04d-%02d-%02d', $year, $matches[2], $matches[1]);
        }

        // 4️⃣ Fallback: coba Carbon parse
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
