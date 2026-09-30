<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi bentuk dasar saja — aturan per-field formulir Jenis Surat (wajib/tipe/opsi) sepenuhnya
 * milik WRGROUP dan diperiksa ulang di sana; response 422 dari sana ditampilkan lewat flash error.
 */
class WrgroupSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'surat_jenis_kode' => 'required|string|max:20',
            'jenis_nama' => 'nullable|string|max:255',
            'perihal' => 'required|string|max:255',
            'data_variabel' => 'nullable|array',
        ];
    }
}
