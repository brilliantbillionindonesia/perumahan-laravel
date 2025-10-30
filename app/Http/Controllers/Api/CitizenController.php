<?php

namespace App\Http\Controllers\Api;

use App\Constants\GenderOption;
use App\Constants\HttpStatusCodes;
use App\Constants\RelationshipStatusOption;
use App\Http\Controllers\Controller;
use App\Http\Repositories\CitizenRepository;
use DB;
use Illuminate\Http\Request;
use Validator;

class CitizenController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'only_head' => ['nullable', 'boolean'],
            'blood_type' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'work_type' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = (int) ($request->input('page', 1));
        $perPage = (int) ($request->input('per_page', 10));
        $data = CitizenRepository::queryCitizen();

        if ($request->input('only_head')) {
            $data->where('fm.relationship_status', RelationshipStatusOption::KEPALA_KELUARGA);
        }

        if ($request->input('blood_type')) {
            $data->where('c.blood_type', $request->input('blood_type'));
        }

        if ($request->input('gender')) {
            $data->where('c.gender', $request->input('gender'));
        }

        if ($request->input('marital_status')) {
            $data->where('c.marital_status', $request->input('marital_status'));
        }

        if ($request->input('work_type')) {
            $data->where('c.work_type', $request->input('work_type'));
        }

        $data->limit($perPage)
            ->offset(($page - 1) * $perPage);

        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('c.fullname', 'like', '%' . $request->input('search') . '%');
                $q->orWhere('hs.house_name', 'like', '%' . $request->input('search') . '%');
            });
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data->get()->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "housing_user_id" => ['required', 'exists:housing_users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = CitizenRepository::queryCitizen()
            ->where('hu.id', $request->input('housing_user_id'))
            ->first();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function statisticHeadCitizen(Request $request)
    {
        $housingId = $request->input('housing_id');

        $kepalaKeluargaGender = DB::table(DB::raw('(
        SELECT
            hu.housing_id,
            hu.citizen_id,
            c.gender
        FROM housing_users AS hu
        JOIN family_members AS fm ON hu.citizen_id = fm.citizen_id
        JOIN citizens AS c ON hu.citizen_id = c.id
        WHERE hu.housing_id = "' . $housingId . '"
        AND fm.relationship_status = "kepala keluarga"
        GROUP BY hu.citizen_id
    ) AS table_head_citizen'))
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(fn($row) => [$row->gender => $row->total]);

        $defaultGenders = [
            GenderOption::LAKILAKI => 0,
            GenderOption::PEREMPUAN => 0,
        ];

        $merged = collect($defaultGenders)->map(function ($default, $gender) use ($kepalaKeluargaGender) {
            return $kepalaKeluargaGender[$gender] ?? 0;
        });

        $merged['Total'] = $merged->sum();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $merged
        ], HttpStatusCodes::HTTP_OK);
    }

    public function statisticCitizen(Request $request)
    {
        $housingId = $request->input('housing_id');
        $citizenGender = DB::table(DB::raw('(
        SELECT
            hu.housing_id,
            hu.citizen_id,
            c.fullname,
            c.gender
        FROM housing_users AS hu
        JOIN family_members AS fm ON hu.citizen_id = fm.citizen_id
        JOIN citizens AS c ON hu.citizen_id = c.id
        WHERE hu.housing_id = "' . $housingId . '"
        GROUP BY hu.citizen_id
    ) AS table_citizens'))
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(fn($row) => [$row->gender => $row->total]);

        $defaultGenders = [
            GenderOption::LAKILAKI => 0,
            GenderOption::PEREMPUAN => 0,
        ];

        $merged = collect($defaultGenders)->map(function ($default, $gender) use ($citizenGender) {
            return $citizenGender[$gender] ?? 0;
        });

        // 🔹 Tambahkan total keseluruhan
        $merged['Total'] = $merged->sum();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $merged
        ], HttpStatusCodes::HTTP_OK);
    }

    public function statisticCitizenAge(Request $request)
    {
        $housingId = $request->input('housing_id');

        // 🔹 Query utama (tetap sama)
        $data = DB::table(DB::raw('(
        SELECT
            hu.housing_id,
            hu.citizen_id,
            c.fullname,
            c.gender,
            TIMESTAMPDIFF(YEAR, c.birth_date, CURDATE()) AS age
        FROM housing_users AS hu
        JOIN family_members AS fm ON hu.citizen_id = fm.citizen_id
        JOIN citizens AS c ON hu.citizen_id = c.id
        WHERE hu.housing_id = "' . $housingId . '"
        GROUP BY hu.citizen_id
    ) AS table_citizens'))
            ->select(
                'gender',
                DB::raw("CASE
            WHEN age < 6 THEN 'Balita'
            WHEN age BETWEEN 6 AND 12 THEN 'Anak-anak'
            WHEN age BETWEEN 13 AND 17 THEN 'Remaja'
            WHEN age BETWEEN 18 AND 59 THEN 'Dewasa Produktif'
            ELSE 'Lansia'
        END AS kategori_usia"),
                DB::raw('COUNT(*) AS total')
            )
            ->groupBy('gender', 'kategori_usia')
            ->orderBy('gender')
            ->orderBy('kategori_usia')
            ->get();

        // 🔹 Gender & kategori default
        $genders = [GenderOption::LAKILAKI, GenderOption::PEREMPUAN];
        $kategoriUsia = ['Balita', 'Anak-anak', 'Remaja', 'Dewasa Produktif', 'Lansia'];

        // 🔹 Inisialisasi data kosong
        $result = [];
        foreach ($genders as $g) {
            foreach ($kategoriUsia as $k) {
                $result[$g][$k] = 0;
            }
        }

        // 🔹 Isi data dari hasil query
        foreach ($data as $row) {
            $gender = $row->gender;
            $kategori = $row->kategori_usia;
            $total = $row->total;

            if (isset($result[$gender][$kategori])) {
                $result[$gender][$kategori] = (int) $total;
            }
        }

        // 🔹 Hitung total per gender
        $final = collect($result)->map(function ($kategori) {
            $kategori['Total'] = array_sum($kategori);
            return $kategori;
        });

        // 🔹 Hitung total per kategori (lintas gender)
        $totalPerKategori = [];
        foreach ($kategoriUsia as $k) {
            $totalPerKategori[$k] = $final->sum($k);
        }

        // 🔹 Tambahkan total keseluruhan di bawah total_per_kategori
        $totalPerKategori['Total'] = array_sum($totalPerKategori);

        // 🔹 Grand total
        $grandTotal = $totalPerKategori['Total'];

        // 🔹 Kembalikan hasil
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => [
                'detail' => $final,
                'total_per_kategori' => $totalPerKategori,
                'grand_total' => $grandTotal,
            ],
        ], HttpStatusCodes::HTTP_OK);
    }

    public function statisticSubdistrictMatch(Request $request)
    {
        $housingId = $request->input('housing_id');

        $sama = 'Sesuai';
        $beda = 'Tidak Sesuai';

        // 🔹 Query utama
        $data = DB::table('housings AS h')
            ->join('housing_users AS hu', 'h.id', '=', 'hu.housing_id')
            ->join('houses AS hus', 'hus.head_citizen_id', '=', 'hu.citizen_id')
            ->join('family_cards AS fc', 'hus.family_card_id', '=', 'fc.id')
            ->select(
                DB::raw("CASE
                WHEN fc.subdistrict_code = h.subdistrict_code THEN '".$sama."'
                ELSE '".$beda."'
            END AS status_kode"),
                DB::raw('COUNT(*) AS total')
            )
            ->where('hu.housing_id', $housingId)
            ->groupBy('status_kode')
            ->orderBy('status_kode')
            ->get();

        $result = [
            $sama => 0,
            $beda => 0,
        ];

        foreach ($data as $row) {
            $status = $row->status_kode;
            $total = $row->total;

            if (isset($result[$status])) {
                $result[$status] = (int) $total;
            }
        }

        // 🔹 Hitung total keseluruhan
        $grandTotal = array_sum($result);

        // 🔹 Kembalikan hasil JSON
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => [
                'status' => $result,
                'grand_total' => $grandTotal,
            ],
        ], HttpStatusCodes::HTTP_OK);
    }
}
