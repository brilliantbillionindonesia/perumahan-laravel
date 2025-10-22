<?php

namespace App\Http\Controllers\Api\Scanner;

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

    public function store(Request $request)
    {
        $this->folder = 'family_cards';

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'house_block' => ['required', 'string'],
            'house_number' => ['required', 'string'],
        ], [
            'file.required' => 'File wajib diisi',
            'file.mimes' => 'Format file tidak sesuai',
            'file.max' => 'Ukuran file tidak boleh lebih dari 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $convert = $this->uploadConvert($request)->original;

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

        $parseJson = $this->parseJson($json, $convert['path']);
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
            'head_citizen_id' => $kepalaKeluarga['citizen_id']
        ]);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data berhasil diolah dan disimpan',
            'data' => $parseJson
        ], HttpStatusCodes::HTTP_OK);
    }

    private function uploadConvert(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // 5 MB limit
            'housing_id' => 'required|string',
        ]);

        $file = $request->file('file');
        $housingId = $request->input('housing_id');
        $ext = strtolower($file->getClientOriginalExtension());

        $folder = $this->folder ?? 'family_cards';
        $filename = Str::random(12) . '.jpg'; // pakai JPG supaya ukuran kecil
        $storagePath = "public/{$folder}/{$housingId}";
        $fullPath = storage_path("app/{$storagePath}");

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        // Setup Intervention Image
        $manager = new ImageManager(new Driver());

        try {
            if ($ext === 'pdf') {
                // === Konversi PDF ke image ===
                $tempPath = $file->store("temp/{$housingId}", 'public');
                $fullPdfPath = storage_path('app/public/' . $tempPath);

                $pdfToImage = new \Spatie\PdfToImage\Pdf($fullPdfPath);
                $imageTemp = "{$fullPath}/temp_{$filename}";
                $pdfToImage->setPage(1)
                    ->setOutputFormat('jpg')
                    ->saveImage($imageTemp);

                Storage::disk('public')->delete($tempPath);

                $image = $manager->read($imageTemp);
            } else {
                // === Jika file gambar biasa ===
                $image = $manager->read($file->getRealPath());
            }

            // === Enhancement dasar (tajam & jelas untuk teks dokumen) ===
            $image->greyscale()
                ->contrast(20)
                ->sharpen(10);

            // === Simpan sementara ===
            $outputPath = "{$fullPath}/{$filename}";
            $image->save($outputPath, quality: 85);

            // === Resize jika > 1MB ===
            $maxSize = 1048576; // 1 MB
            $finalSize = filesize($outputPath);
            $quality = 85;

            while ($finalSize > $maxSize && $quality > 50) {
                $quality -= 5;
                // turunkan lebar sedikit biar makin kecil
                $image->scale(width: intval($image->width() * 0.9));
                $image->save($outputPath, quality: $quality);
                $finalSize = filesize($outputPath);
            }

            // Hapus temp image dari PDF (kalau ada)
            if (isset($imageTemp) && file_exists($imageTemp)) {
                unlink($imageTemp);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses file: ' . $e->getMessage(),
            ], 500);
        }

        $publicUrl = asset("storage/{$folder}/{$housingId}/{$filename}");

        return response()->json([
            'status' => 'success',
            'message' => 'File berhasil disimpan & dioptimasi',
            'filename' => $filename,
            'extension' => 'jpg',
            'size_kb' => round($finalSize / 1024, 2),
            'image_url' => $publicUrl,
            'path' => "{$folder}/{$housingId}/{$filename}"
        ]);
    }

    private function sendToAi($convert, $housingId)
    {
        $fullPath = $convert['path'];
        $filename = $convert['filename'];
        $scannerUrl = config('services.scanner.url') . '/scan-kk-full';

        if (!Storage::disk('public')->exists($fullPath)) {
            throw new \Exception("File {$fullPath} tidak ditemukan di storage");
        }

        $imageUrl = $convert['image_url'];
        $pythonResp = Http::
            timeout(120)
            ->attach(
                'file',
                file_get_contents(storage_path('app/public/' . $fullPath)),
                $filename
            )->post($scannerUrl);

        if ($pythonResp->failed()) {
            throw new \Exception("Gagal mengirim file ke AI: " . $pythonResp->body());
        }

        $data = $pythonResp->json();
        $jsonFilename = 'result-' . Str::random(8) . '.json';
        $jsonPath = "scanner_json/{$housingId}/{$jsonFilename}";
        Storage::put($jsonPath, json_encode($data, JSON_PRETTY_PRINT));
        return $jsonPath;
    }

    private function parseJson($fileLocation, $pathImage)
    {
        $filePath = Storage::get($fileLocation);
        $jsonDecode = json_decode($filePath, true);
        $dataFamilyCard = $jsonDecode['data_kk'];
        $mainData = $dataFamilyCard['data_utama'];
        $dataMember = $dataFamilyCard['anggota_keluarga'];
        $dataPernikahan = $dataFamilyCard['status_perkawinan'];

        $explodeRtRw = explode('/', $mainData['rt_rw']);
        $village = Village::where('name', $mainData['kelurahan'])->first();

        $parsedFamilyCard = [
            'family_card_number' => $mainData['nomor_kk'],
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

        $createFamilyDoc = FamilyDocument::updateOrCreate(
            [
                'family_card_id' => $createFamilyCard->id
            ],
            [
                'family_card_id' => $createFamilyCard->id,
                'doc_name' => 'Kartu Keluarga',
                'doc_file' => $pathImage,
                'is_ai_generated' => true
            ]
        );

        $createCitizens = [];
        $createFamilyMembers = [];
        foreach ($dataMember as $key => $itemMember) {
            $dataRelationshipStatus = $dataPernikahan[$key];
            $parsedCitizen = [
                'family_card_id' => $createFamilyCard->id,
                'citizen_card_number' => $itemMember['nik'],
                'fullname' => ucwords(strtolower($itemMember['nama_lengkap'])),
                'gender' => GenderOption::getTypeOption($itemMember['jenis_kelamin']),
                'birth_place' => ucwords(strtolower($itemMember['tempat_lahir'])),
                'birth_date' => Carbon::parse($itemMember['tanggal_lahir'])->format('Y-m-d'),
                'blood_type' => strlen($itemMember['golongan_darah']) > 0 ? $itemMember['golongan_darah'] : "-",
                'religion' => ReligionOption::getTypeOption($itemMember['agama']),
                'marital_status' => MaritalStatusOption::getTypeOption($dataRelationshipStatus['status_perkawinan']),
                'work_type' => WorkTypeOption::getTypeOption($itemMember['jenis_pekerjaan']),
                'education_type' => EducationTypeOption::getTypeOption($itemMember['pendidikan']),
                'citizenship' => $dataRelationshipStatus['kewarganegaraan'],
                'is_ai_generated' => true
            ];

            $checkNikNameCitizen = Citizen::where('citizen_card_number', $itemMember['nik'])
                ->orWhere('fullname', $parsedCitizen['fullname'])
                ->first();

            if(!$checkNikNameCitizen){
                $checkNikNameCitizen = Citizen::where('fullname', $parsedCitizen['fullname'])
                ->first();
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
}
