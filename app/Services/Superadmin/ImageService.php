<?php

namespace App\Services\Superadmin;

use App\Models\DataLapangan;

class ImageService
{
    /**
     * Get foto KTP path and validate existence
     */
    public function getFotoKTPPath(DataLapangan $dataLapangan): ?string
    {
        if (!$dataLapangan->foto_ktp) {
            return null;
        }

        // Check in public/storage first
        if (file_exists(public_path('storage/' . $dataLapangan->foto_ktp))) {
            return public_path('storage/' . $dataLapangan->foto_ktp);
        }

        // Then check in storage/app/public
        if (file_exists(storage_path('app/public/' . $dataLapangan->foto_ktp))) {
            return storage_path('app/public/' . $dataLapangan->foto_ktp);
        }

        return null;
    }

    /**
     * Compress and create temporary KTP image
     */
    public function compressKTPImage(string $fotoPath, string $id): string
    {
        // Detect image type
        $imageInfo = getimagesize($fotoPath);
        $mimeType = $imageInfo['mime'];

        // Load image based on type
        $image = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($fotoPath),
            'image/png' => imagecreatefrompng($fotoPath),
            'image/gif' => imagecreatefromgif($fotoPath),
            default => throw new \Exception('Format gambar tidak didukung')
        };

        // Compress and save temporary
        $tempPath = storage_path('app/temp_ktp_' . $id . '.jpg');
        $quality = 85;

        imagejpeg($image, $tempPath, $quality);

        // Check size and compress more if needed
        while (filesize($tempPath) > 2 * 1024 * 1024 && $quality > 50) {
            $quality -= 5;
            imagejpeg($image, $tempPath, $quality);
        }

        imagedestroy($image);

        return $tempPath;
    }

    /**
     * Generate safe filename for download
     */
    public function generateSafeFilename(string $name, string $prefix = 'KTP', string $extension = 'jpg'): string
    {
        $fileName = $prefix . '_' . $name . '.' . $extension;
        return preg_replace('/[^A-Za-z0-9_\-.]/', '_', $fileName);
    }

    /**
     * Get foto rumah path
     */
    public function getFotoRumahPath(DataLapangan $dataLapangan): ?string
    {
        if (!$dataLapangan->foto_rumah) {
            return null;
        }

        $path = storage_path('app/public/' . $dataLapangan->foto_rumah);

        return file_exists($path) ? $path : null;
    }

    /**
     * Convert image to base64
     */
    public function convertImageToBase64(string $imagePath): array
    {
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $imageSrc = 'data:' . $imageMimeType . ';base64,' . $imageData;

        return [
            'data' => $imageData,
            'mime_type' => $imageMimeType,
            'src' => $imageSrc
        ];
    }
}
