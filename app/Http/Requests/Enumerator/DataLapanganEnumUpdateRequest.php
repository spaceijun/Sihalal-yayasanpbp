<?php

namespace App\Http\Requests\Enumerator;

use App\Services\Enumerator\DataLapanganEnumService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi PUT/PATCH /api/enumerator/data-lapangan/{id}. Dipindah dari Validator::make inline di
 * DataLapanganEnumController — lihat .agent/workflows/data-lapangan-enumerator-api.md §2.
 *
 * Geotag di sini OPSIONAL (beda dari StoreRequest) — update tidak selalu berarti enumerator
 * sedang di lokasi, lihat §3.2 dokumen yang sama.
 */
class DataLapanganEnumUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Rule foto dibangun dinamis, hanya untuk file yang benar-benar dikirim (perilaku lama
        // dipertahankan — enumerator boleh update sebagian field tanpa mengirim ulang semua foto).
        $fotoRules = [];
        foreach (DataLapanganEnumService::FOTO_FIELDS as $inputKey => $dbColumn) {
            if ($this->hasFile($inputKey)) {
                $fotoRules[$inputKey] = 'image|mimes:jpg,jpeg,png|max:2048';
            }
        }
        if ($this->hasFile('file_oss')) {
            $fotoRules['file_oss'] = 'file|mimes:pdf|max:5120';
        }

        return array_merge([
            'nama_pu' => 'sometimes|required|string|max:255',
            'nik' => 'sometimes|required|string|size:16',
            'telephone' => 'sometimes|required|string|max:15',
            'nama_produk' => 'sometimes|required|string|max:255',
            'alamat' => 'sometimes|required|string',

            // Geotag — opsional saat update
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'akurasi_meter' => 'nullable|numeric|min:0',
            'geotag_captured_at' => 'nullable|date',

            // Address fields
            'provinsi' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kelurahan' => 'nullable|string|max:100',
            'rt' => 'nullable|string|size:3',
            'rw' => 'nullable|string|size:3',
            'kode_pos' => 'nullable|string|max:5',
            'tanggal_lahir' => 'nullable|date',
            'has_nib' => 'sometimes|in:true,false,1,0',
            'nama_produk_2' => 'nullable|string|max:255',
            'nama_produk_3' => 'nullable|string|max:255',
            'nama_produk_4' => 'nullable|string|max:255',
            'nama_produk_5' => 'nullable|string|max:255',
        ], $fotoRules);
    }
}
