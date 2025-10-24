<?php

namespace App\Http\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;

class UploadConvertImageService
{

    private static $folder;

    public static function setFolder($folder)
    {
        self::$folder = $folder;
        return new self();
    }

    public static function uploadConvert(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // 5 MB limit
            'housing_id' => 'required|string',
        ]);

        $file = $request->file('file');
        $housingId = $request->input('housing_id');
        $ext = strtolower($file->getClientOriginalExtension());

        $folder = self::$folder ?? 'family_cards';
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

}
