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

        // Store new file
        $path = $file->store('files/' . $fileType, 'public');

        // Simpan path ke kolom yang sesuai dan persist ke database
        $dataLapangan->$fieldName = $path;
        $dataLapangan->save();

        return [
            'path'           => $path,
            'is_first_upload' => $isFirstUpload,
            'field_name'     => $fieldName,
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
     * Upload image sequentially (foto_ktp, foto_rumah, etc)
     */
    public function uploadImageSequential(UploadedFile $image, string $type): string
    {
        $extension = $image->getClientOriginalExtension();
        $imageName = time() . '_' . uniqid() . '.' . $extension;

        // Convert type name to folder name (foto_ktp -> foto-ktp)
        $folderName = str_replace('_', '-', $type);
        $image->storeAs($folderName, $imageName, 'public');

        return $folderName . '/' . $imageName;
    }

    /**
     * Validate allowed file types for sequential upload
     */
    public function isAllowedType(string $type): bool
    {
        $allowedTypes = ['foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_produk'];

        return in_array($type, $allowedTypes);
    }
}
