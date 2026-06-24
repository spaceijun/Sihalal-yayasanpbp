<?php

namespace App\Services\Superadmin;

use App\Models\DataLapangan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Upload file (OSS or SIHALAL)
     */
    public function uploadFile(DataLapangan $dataLapangan, UploadedFile $file, string $fileType): array
    {
        $fieldName     = 'file_' . $fileType;
        $isFirstUpload = is_null($dataLapangan->$fieldName);

        // Delete old file if exists
        if ($dataLapangan->$fieldName) {
            Storage::delete($dataLapangan->$fieldName);
        }

        // Generate nama file: SH-nama_pu.pdf
        $safeName  = preg_replace('/[^A-Za-z0-9\-_]/', '_', $dataLapangan->nama_pu);
        $fileName  = 'SH-' . $safeName . '.' . $file->getClientOriginalExtension();
        $directory = 'files/' . $fileType;

        // Store dengan nama custom
        $path = $file->storeAs($directory, $fileName, 'public');

        // Simpan path ke kolom yang sesuai dan persist ke database
        $dataLapangan->$fieldName = $path;
        $dataLapangan->save();

        return [
            'path'            => $path,
            'is_first_upload' => $isFirstUpload,
            'field_name'      => $fieldName,
        ];
    }

    /**
     * Delete file (OSS or SIHALAL)
     */
    public function deleteFile(DataLapangan $dataLapangan, string $fileType): bool
    {
        $fieldName = 'file_' . $fileType;
        if ($dataLapangan->$fieldName) {
            Storage::delete($dataLapangan->$fieldName);
            $dataLapangan->$fieldName = null;
            $dataLapangan->save();
            return true;
        }
        return false;
    }

    /**
     * Upload image sequentially (foto_ktp, foto_rumah, foto_produk, foto_produk_2, dst.)
     * Semua foto_produk_* disimpan dalam satu folder: foto-produk
     */
    public function uploadImageSequential(UploadedFile $image, string $type): string
    {
        $extension  = $image->getClientOriginalExtension();
        $imageName  = time() . '_' . uniqid() . '.' . $extension;

        // Semua foto_produk_* masuk ke folder foto-produk
        $folderName = str_starts_with($type, 'foto_produk')
            ? 'foto-produk'
            : str_replace('_', '-', $type);

        $image->storeAs($folderName, $imageName, 'public');

        return $folderName . '/' . $imageName;
    }

    /**
     * Validate allowed file types for sequential upload.
     * Mencakup foto_produk sampai foto_produk_5.
     */
    public function isAllowedType(string $type): bool
    {
        $allowedTypes = [
            'foto_ktp',
            'foto_rumah',
            'foto_pendamping',
            'foto_proses',
            'foto_produk',
            'foto_produk_2',
            'foto_produk_3',
            'foto_produk_4',
            'foto_produk_5',
        ];
        return in_array($type, $allowedTypes);
    }
}
