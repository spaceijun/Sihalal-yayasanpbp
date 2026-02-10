<?php

namespace App\Services\Superadmin;

use App\Models\DataLapangan;
use Illuminate\Support\Facades\Storage;

class ImageDownloadService
{
    /**
     * Get the path of foto pendamping
     */
    public function getFotoPendampingPath(DataLapangan $dataLapangan): ?string
    {
        if (!$dataLapangan->foto_pendamping) {
            return null;
        }

        $path = storage_path('app/public/' . $dataLapangan->foto_pendamping);

        if (!file_exists($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Get the path of foto produk
     */
    public function getFotoProdukPath(DataLapangan $dataLapangan): ?string
    {
        if (!$dataLapangan->foto_produk) {
            return null;
        }

        $path = storage_path('app/public/' . $dataLapangan->foto_produk);

        if (!file_exists($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Compress image for download using GD library
     */
    public function compressImage(string $sourcePath, int $dataLapanganId, string $type = 'pendamping'): string
    {
        $tempDir = storage_path('app/temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/foto_' . $type . '_' . $dataLapanganId . '_' . time() . '.jpg';

        try {
            // Get image info
            $imageInfo = getimagesize($sourcePath);
            if ($imageInfo === false) {
                throw new \Exception('File bukan gambar yang valid');
            }

            $mimeType = $imageInfo['mime'];

            // Create image resource based on mime type
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    throw new \Exception('Format gambar tidak didukung');
            }

            if ($sourceImage === false) {
                throw new \Exception('Gagal membaca gambar');
            }

            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Calculate new dimensions (max 1920px)
            $maxDimension = 1920;
            if ($originalWidth > $maxDimension || $originalHeight > $maxDimension) {
                $ratio = min($maxDimension / $originalWidth, $maxDimension / $originalHeight);
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Resize image
            imagecopyresampled(
                $newImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // Save as JPEG with 85% quality
            imagejpeg($newImage, $tempPath, 85);

            // Free memory
            imagedestroy($sourceImage);
            imagedestroy($newImage);

            return $tempPath;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    /**
     * Generate safe filename for download
     */
    public function generateSafeFilename(string $namaPU, string $type = 'Pendamping'): string
    {
        $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $namaPU);
        return 'Foto_' . $type . '_' . $cleanName . '.jpg';
    }

    /**
     * Download foto pendamping
     */
    public function downloadFotoPendamping(DataLapangan $dataLapangan)
    {
        $fotoPath = $this->getFotoPendampingPath($dataLapangan);

        if (!$fotoPath) {
            throw new \Exception('File foto pendamping tidak ditemukan');
        }

        $tempPath = $this->compressImage($fotoPath, $dataLapangan->id, 'pendamping');
        $fileName = $this->generateSafeFilename($dataLapangan->nama_pu, 'Pendamping');

        return [
            'path' => $tempPath,
            'filename' => $fileName
        ];
    }

    /**
     * Download foto produk
     */
    public function downloadFotoProduk(DataLapangan $dataLapangan)
    {
        $fotoPath = $this->getFotoProdukPath($dataLapangan);

        if (!$fotoPath) {
            throw new \Exception('File foto produk tidak ditemukan');
        }

        $tempPath = $this->compressImage($fotoPath, $dataLapangan->id, 'produk');
        $fileName = $this->generateSafeFilename($dataLapangan->nama_pu, 'Produk');

        return [
            'path' => $tempPath,
            'filename' => $fileName
        ];
    }
}
