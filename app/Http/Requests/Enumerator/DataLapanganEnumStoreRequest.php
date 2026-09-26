<?php

namespace App\Http\Requests\Enumerator;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi POST /api/enumerator/data-lapangan (submit data lapangan baru oleh Enumerator).
 * Dipindah dari Validator::make inline di DataLapanganEnumController — lihat
 * .agent/workflows/data-lapangan-enumerator-api.md §2.
 *
 * Geotag (latitude/longitude) WAJIB di sini — lihat §3.2 dokumen yang sama untuk konsekuensi
 * breaking-change terhadap versi app Flutter yang belum mengirim field ini.
 */
class DataLapanganEnumStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi ditegakkan oleh middleware route (auth:sanctum, role:enumerator, enumerator.active).
        return true;
    }

    public function rules(): array
    {
        return [
            // Data wajib
            'nama_pu' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'telephone' => 'required|string|max:15',
            'nama_produk' => 'required|string|max:255',
            'alamat' => 'required|string',
            'foto-ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-rumah' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-pendamping' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-proses' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto-produk' => 'required|image|mimes:jpg,jpeg,png|max:2048',

            // Geotag — wajib saat submit (§3 data-lapangan-enumerator-api.md)
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
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

            // NIB
            'has_nib' => 'required|in:true,false,1,0',
            'file_oss' => 'nullable|file|mimes:pdf|max:5120|required_if:has_nib,true,has_nib,1',

            // Produk tambahan (opsional)
            'nama_produk_2' => 'nullable|string|max:255',
            'nama_produk_3' => 'nullable|string|max:255',
            'nama_produk_4' => 'nullable|string|max:255',
            'nama_produk_5' => 'nullable|string|max:255',

            // Foto produk tambahan — wajib jika nama produk yang bersangkutan diisi
            'foto-produk-2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_2',
            'foto-produk-3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_3',
            'foto-produk-4' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_4',
            'foto-produk-5' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|required_with:nama_produk_5',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Lokasi GPS (latitude) wajib dikirim saat submit data lapangan.',
            'longitude.required' => 'Lokasi GPS (longitude) wajib dikirim saat submit data lapangan.',
            'latitude.between' => 'Nilai latitude tidak valid.',
            'longitude.between' => 'Nilai longitude tidak valid.',
        ];
    }
}
