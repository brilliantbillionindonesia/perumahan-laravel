<?php

namespace App\Http\Controllers\Api\Scanner;

use App\Constants\BloodTypeOption;
use App\Constants\CitizenshipOption;
use App\Constants\EducationTypeOption;
use App\Constants\GenderOption;
use App\Constants\HttpStatusCodes;
use App\Constants\MaritalStatusOption;
use App\Constants\RelationshipStatusOption;
use App\Constants\ReligionOption;
use App\Constants\WorkTypeOption;
use App\Http\Controllers\Controller;
use App\Http\Services\UploadConvertImageService;
use App\Models\Citizen;
use App\Models\FamilyCard;
use App\Models\FamilyCardScannedDoc;
use App\Models\FamilyDocument;
use App\Models\FamilyMember;
use App\Models\House;
use App\Models\HousingUser;
use App\Models\Village;
use Carbon\Carbon;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FamilyCardScannerController extends Controller
{
    protected $folder = 'family_cards';

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = max((int) $request->get('page', 1), 1);
        $perPage = min((int) $request->get('per_page', 10), 30);

        $data = FamilyCardScannedDoc::selectRaw(
            "family_card_scanned_docs.id,housing_id,CONCAT(house_block, '|', house_number) AS house_label,house_block,house_number,ownership_status,verified_at,verified_by,verified.name as verified_name,accuracy"
        )->where('housing_id', $request->input('housing_id'))
            ->leftjoin('users as verified', 'family_card_scanned_docs.verified_by', '=', 'verified.id');
        $data->when($request->input('is_verified'), function ($query) use ($request) {
            if ($request->input('is_verified') == true) {
                $query->whereNotNull('verified_at');
            } else if ($request->input('is_verified') == false) {
                $query->whereNull('verified_at');
            }
        });
        $data->when($request->input('search'), function ($query) use ($request) {
            $query->having('house_label', 'like', '%' . $request->input(key: 'search') . '%');

        });

        $data->limit($perPage)
            ->offset(($page - 1) * $perPage);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data->get()->toArray()
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:family_card_scanned_docs,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = FamilyCardScannedDoc::
            selectRaw('family_card_scanned_docs.id,housing_id,family_card_id,house_block,house_number,phone_number,ownership_status,data_json,data_json_verified,submitted_at,verified_by,verified.name as verified_name,verified_at,accuracy')
            ->where('family_card_scanned_docs.id', $request->input('id'))
            ->where('housing_id', $request->input('housing_id'))
            ->leftjoin('users as verified', 'family_card_scanned_docs.verified_by', '=', 'verified.id')
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'data' => $data
        ], HttpStatusCodes::HTTP_OK);
    }

    public function getFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:family_card_scanned_docs,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $data = FamilyCardScannedDoc::select('id', 'path')->where('id', $request->id)
            ->where('housing_id', $request->housing_id)
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $path = $data->path;

        if (!Storage::disk('private')->exists($path)) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => "File tidak ditemukan",
            ], 404);
        }

        // Stream inline
        return response()->file(
            Storage::disk('private')->path($path)
        );
    }
    public function store(Request $request)
    {
        $this->folder = 'family_cards';

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'house_block' => ['required', 'string'],
            'house_number' => ['required', 'string'],
            'ownership_status' => ['required', 'string', 'in:own,rent'],
            'phone_number' => [
                'required',
                'regex:/^(\+628|628|08)[0-9]{7,13}$/'
            ],
        ], [
            'file.required' => 'File wajib diisi',
            'file.mimes' => 'Format file tidak sesuai',
            'file.max' => 'Ukuran file tidak boleh lebih dari 5MB',
            'ownership.in' => 'Pemilik harus diisi',
            'phone_number.required' => 'Nomor telepon wajib diisi',
            'phone_number.regex' => 'Nomor telepon tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $convert = UploadConvertImageService::setFolder($this->folder)->uploadConvert($request)->original;
        if ($convert['status'] == 'error') {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $convert['message'],
            ]);
        }

        try {
            $json = $this->sendToAi($convert, $request->input('housing_id'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage(),
            ]);
        }

        $filePath = Storage::get($json);
        $jsonDecode = json_decode($filePath, true);
        $data = [
            'housing_id' => $request->input('housing_id'),
            'house_block' => $request->input('house_block'),
            'house_number' => $request->input('house_number'),
            'ownership_status' => $request->input('ownership_status'),
            'file_name' => $originalName,
            'phone_number' => $request->input('phone_number'),
            'path' => $convert['path'],
            'data_json' => $jsonDecode,
            'submitted_at' => Carbon::now()->toDateTimeString(),
        ];

        $create = FamilyCardScannedDoc::create($data);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil diolah dengan AI dan disimpan. Silahkan lakukan verifikasi data.',
            'data' => [
                'id' => $create->id,
            ]
        ], HttpStatusCodes::HTTP_OK);
    }

    public function update(Request $request)
    {
        $this->folder = 'family_cards';

        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:family_card_scanned_docs,id'],
            'file' => ['required', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'house_block' => ['required', 'string'],
            'house_number' => ['required', 'string'],
            'ownership_status' => ['required', 'string', 'in:own,rent'],
        ], [
            'file.required' => 'File wajib diisi',
            'file.mimes' => 'Format file tidak sesuai',
            'file.max' => 'Ukuran file tidak boleh lebih dari 5MB',
            'ownership.in' => 'Pemilik harus diisi'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $check = FamilyCardScannedDoc::where('id', $request->input('id'))
            ->first();

        if (!$check) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($check->verified_at != null) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Data sudah diverifikasi',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $convert = UploadConvertImageService::setFolder($this->folder)->uploadConvert($request)->original;
        if ($convert['status'] == 'error') {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $convert['message'],
            ]);
        }

        try {
            $json = $this->sendToAi($convert, $request->input('housing_id'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage(),
            ]);
        }

        $filePath = Storage::get($json);
        $jsonDecode = json_decode($filePath, true);
        $data = [
            'housing_id' => $request->input('housing_id'),
            'house_block' => $request->input('house_block'),
            'house_number' => $request->input('house_number'),
            'ownership_status' => $request->input('ownership_status'),
            'file_name' => $originalName,
            'path' => $convert['path'],
            'data_json' => $jsonDecode,
            'submitted_at' => Carbon::now()->toDateTimeString(),
        ];

        $check->update($data);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil diolah dengan AI dan disimpan. Silahkan lakukan verifikasi data.',
            'data' => [
                'id' => $check->id,
            ]
        ], HttpStatusCodes::HTTP_OK);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required', 'exists:family_card_scanned_docs,id'],
            "house_block" => ['required', 'string'],
            "house_number" => ['required', 'integer'],
            "ownership_status" => ['required', 'string', 'in:own,rent'],
            "data_json" => ['required', 'array'],
            'phone_number' => [
                'required',
                'regex:/^(\+628|628|08)[0-9]{7,13}$/'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }


        $check = FamilyCardScannedDoc::where('id', $request->input('id'))
            ->where('housing_id', $request->input('housing_id'))
            ->first();

        if (!$check) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        // $checkHouse = FamilyCardScannedDoc::where('house_block', $request->input('house_block'))
        //     ->where('house_number', $request->input('house_number'))
        //     ->where('id', '!=', $request->input('id'))
        //     ->where('housing_id', $request->input('housing_id'))
        //     ->first();

        // if ($checkHouse) {
        //     return response()->json([
        //         'success' => false,
        //         'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
        //         'message' => 'Nomor rumah sudah terdaftar',
        //     ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        // }

        $parsedJson = $this->parseDataJson($request->input('data_json'), $check->path, $request);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil diverifikasi',
            'data' => $parsedJson
        ], HttpStatusCodes::HTTP_OK);

    }

    public function parseDataJson($json, $path, $request)
    {
        $parseJson = $this->parseJsonFile($json, $path, $request);
        foreach ($parseJson['citizen'] as $key => $citizen) {
            HousingUser::updateOrCreate([
                'citizen_id' => $citizen['id'],
                'housing_id' => $request->input('housing_id'),
            ], [
                'role_code' => 'citizen',
                'is_active' => true
            ]);
        }

        $kepalaKeluarga = collect($parseJson['family_member'])
            ->firstWhere('relationship_status', 'Kepala Keluarga');

        $block = $request->input('house_block');
        $number = $request->input('house_number');
        House::updateOrCreate([
            'housing_id' => $request->input('housing_id'),
            'family_card_id' => $parseJson['family_card']['id']
        ], [
            'house_name' => 'Rumah ' . $block . '-' . $number,
            'block' => $block,
            'number' => $number,
            'head_citizen_id' => $kepalaKeluarga['citizen_id'],
            'owner_citizen_id' => $request->input('ownership_status') == 'own' ? $kepalaKeluarga['citizen_id'] : null,
            'renter_citizen_id' => $request->input('ownership_status') == 'rent' ? $kepalaKeluarga['citizen_id'] : null,
        ]);

        $data = [
            'family_card_id' => $parseJson['family_card']['id'],
            'house_block' => $request->input('house_block'),
            'house_number' => $request->input('house_number'),
            'ownership_status' => $request->input('ownership_status'),
            'data_json_verified' => $request->input('data_json'),
            'verified_at' => Carbon::now()->toDateTimeString(),
            'verified_by' => auth()->user()->id
        ];

        $dataScanned = FamilyCardScannedDoc::where('id', $request->input('id'));
        $dataScanned->update($data);
        $dataScanned = $dataScanned->first();
        $accuracy = $this->scannedAccuracy(json_encode($dataScanned->data_json), json_encode($dataScanned->data_json_verified));
        $dataScanned->update(['accuracy' => $accuracy]);
        return [
            'family_card_id' => $dataScanned->family_card_id,
        ];
    }

    private function sendToAi($convert, $housingId)
    {
        $fullPath = $convert['path'];
        $filename = $convert['filename'];
        $scannerUrl = config('services.scanner.url') . '/scan-kk-full';
        // $scannerUrl = config('services.scanner.url') . '/scan-kk-url';

        if (!Storage::disk('private')->exists($fullPath)) {
            throw new \Exception("File {$fullPath} tidak ditemukan di storage");
        }

        $imageUrl = $convert['image_url'];
        $pythonResp = Http::
            timeout(120)
            ->attach(
                'file',
                // 'image_url',
                file_get_contents(storage_path('app/private/' . $fullPath)),
                // $imageUrl,
                $filename
            )->post($scannerUrl);
        if ($pythonResp->failed()) {
            throw new \Exception("Gagal mengirim file ke AI: " . $pythonResp->body());
        }

        $data = $pythonResp->json();
        if (isset($data['data_kk']['error'])) {
            throw new \Exception("Maaf, kami tidak dapat membantu dengan file ini. Silahkan coba dengan file lain.");
        }
        $jsonFilename = 'result-' . Str::random(8) . '.json';
        $jsonPath = "scanner_json/{$housingId}/{$jsonFilename}";
        Storage::put($jsonPath, json_encode($data, JSON_PRETTY_PRINT));
        return $jsonPath;
    }

    private function parseJsonFile($json, $pathImage = null, $request)
    {
        $dataFamilyCard = $json['data_kk'];
        $mainData = $dataFamilyCard['data_utama'];
        $dataMember = $dataFamilyCard['anggota_keluarga'];
        $dataPernikahan = $dataFamilyCard['status_perkawinan'];

        $explodeRtRw = explode('/', $mainData['rt_rw']);
        $village = Village::where('name', $mainData['kelurahan'])->first();

        $parsedFamilyCard = [
            'family_card_number' => $mainData['nomor_kk'],
            'phone_number' => $request['phone_number'],
            'address' => $mainData['alamat'],
            'rt' => $explodeRtRw[0],
            'rw' => $explodeRtRw[1],
            'village_code' => $village->code ?? null,
            'subdistrict_code' => $village->subdistrict_code ?? null,
            'district_code' => $village->district_code ?? null,
            'province_code' => $village->province_code ?? null,
            'postal_code' => $mainData['kode_pos'],
            'is_ai_generated' => true
        ];

        $createFamilyCard = FamilyCard::updateOrCreate(
            [
                'family_card_number' => $mainData['nomor_kk'],
            ],
            $parsedFamilyCard
        );

        $createFamilyDoc = null;
        if ($pathImage) {
            $createFamilyDoc = FamilyDocument::updateOrCreate(
                [
                    'family_card_id' => $createFamilyCard->id
                ],
                [
                    'family_card_id' => $createFamilyCard->id,
                    'doc_name' => 'Kartu Keluarga',
                    'doc_file' => $pathImage ?? null,
                    'is_ai_generated' => true
                ]
            );
        }

        $createCitizens = [];
        $createFamilyMembers = [];
        foreach ($dataMember as $key => $itemMember) {
            $dataRelationshipStatus = $dataPernikahan[$key];
            $raw = $dataRelationshipStatus['tanggal_perkawinan'] ?? null;

            $marriageDate = strtotime($raw)
                ? Carbon::parse($raw)->format('Y-m-d')
                : null;
            $parsedCitizen = [
                'family_card_id' => $createFamilyCard->id,
                'citizen_card_number' => $itemMember['nik'],
                'fullname' => ucwords(strtolower($itemMember['nama_lengkap'])),
                'gender' => GenderOption::getTypeOption($itemMember['jenis_kelamin']),
                'birth_place' => ucwords(strtolower($itemMember['tempat_lahir'])),
                'birth_date' => Carbon::parse($itemMember['tanggal_lahir'])->format('Y-m-d'),
                // 'blood_type' => strlen($itemMember['golongan_darah']) > 0 ? $itemMember['golongan_darah'] : "-",
                'blood_type' => BloodTypeOption::getTypeOption($itemMember['golongan_darah']),
                'religion' => ReligionOption::getTypeOption($itemMember['agama']),
                'marital_status' => MaritalStatusOption::getTypeOption($dataRelationshipStatus['status_perkawinan']),
                'marriage_date' => $marriageDate,
                'work_type' => WorkTypeOption::getTypeOption($itemMember['jenis_pekerjaan']),
                'education_type' => EducationTypeOption::getTypeOption($itemMember['pendidikan']),
                'citizenship' => CitizenshipOption::getTypeOption($dataRelationshipStatus['kewarganegaraan']),
                'is_ai_generated' => 1
            ];

            $checkNikNameCitizen = Citizen::where('citizen_card_number', $itemMember['nik'])
                ->orWhere('fullname', $parsedCitizen['fullname'])
                ->first();

            if (!$checkNikNameCitizen) {
                $checkNikNameCitizen = Citizen::where('fullname', $parsedCitizen['fullname'])->first();
            }

            if (!$checkNikNameCitizen) {
                $createCitizen = Citizen::create($parsedCitizen);
            } else {
                $checkNikNameCitizen->update($parsedCitizen);
                $createCitizen = $checkNikNameCitizen;
            }

            $parsedMember = [
                'citizen_id' => $createCitizen->id,
                'relationship_status' => RelationshipStatusOption::getTypeOption($dataRelationshipStatus['status_dalam_keluarga']),
                'father_name' => ucwords(strtolower($dataRelationshipStatus['ayah'])),
                'mother_name' => ucwords(strtolower($dataRelationshipStatus['ibu'])),
                'is_ai_generated' => true
            ];

            $createFamilyMember = FamilyMember::updateOrCreate(
                [
                    'citizen_id' => $createCitizen->id
                ],
                $parsedMember
            );

            $createCitizens[] = $createCitizen;
            $createFamilyMembers[] = $createFamilyMember;
        }

        return [
            'family_card' => $createFamilyCard,
            'family_document' => $createFamilyDoc,
            'citizen' => $createCitizens,
            'family_member' => $createFamilyMembers,
        ];
    }

    function deepCompare($a, $b, &$total = 0, &$match = 0)
    {
        if (!is_array($a) || !is_array($b)) {
            $total++;
            if ($a === $b)
                $match++;
            return;
        }

        $keys = array_unique(array_merge(array_keys($a), array_keys($b)));

        foreach ($keys as $key) {
            $valA = $a[$key] ?? null;
            $valB = $b[$key] ?? null;

            if (is_array($valA) || is_array($valB)) {
                $this->deepCompare($valA ?? [], $valB ?? [], $total, $match);
            } else {
                $total++;
                if ($valA === $valB)
                    $match++;
            }
        }
    }

    function scannedAccuracy($jsonA, $jsonB)
    {
        $arrA = json_decode($jsonA, true);
        $arrB = json_decode($jsonB, true);

        $total = 0;
        $match = 0;

        $this->deepCompare($arrA, $arrB, $total, $match);

        return $total > 0
            ? round(($match / $total) * 100, 2)
            : 0;
    }
}
