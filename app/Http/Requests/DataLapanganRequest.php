<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataLapanganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Wajib
            'enumerator_id' => 'required',
            'nama_pu' => 'required|string',
            'nik' => 'required|digits:16',
            'telephone' => 'required|string',
            'nama_produk' => 'required|string',
            'alamat' => 'required|string',
            'foto_ktp_path' => 'required|string',
            'foto_rumah_path' => 'required|string',
            'foto_pendamping_path' => 'required|string',
            'foto_produk_path' => 'required|string',
            'foto_proses_path' => 'nullable|string',

            // Produk tambahan (opsional)
            'nama_produk_2' => 'nullable|string|max:255',
            'nama_produk_3' => 'nullable|string|max:255',
            'nama_produk_4' => 'nullable|string|max:255',
            'nama_produk_5' => 'nullable|string|max:255',

            // Foto produk tambahan — wajib jika nama produk yang bersangkutan diisi
            'foto_produk_2_path' => 'nullable|string|required_with:nama_produk_2',
            'foto_produk_3_path' => 'nullable|string|required_with:nama_produk_3',
            'foto_produk_4_path' => 'nullable|string|required_with:nama_produk_4',
            'foto_produk_5_path' => 'nullable|string|required_with:nama_produk_5',
        ];
    }

    public function messages(): array
    {
        return [
            // Pesan wajib
            'enumerator_id.required' => 'Enumerator wajib diisi.',
            'nama_pu.required' => 'Nama PU wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'telephone.required' => 'Nomor Telepon wajib diisi.',
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'foto_ktp_path.required' => 'Foto KTP wajib diunggah.',
            'foto_rumah_path.required' => 'Foto Rumah wajib diunggah.',
            'foto_pendamping_path.required' => 'Foto Pendamping wajib diunggah.',
            'foto_produk_path.required' => 'Foto Produk wajib diunggah.',

            // Pesan foto produk tambahan
            'foto_produk_2_path.required_with' => 'Foto Produk 2 wajib diunggah jika Nama Produk 2 diisi.',
            'foto_produk_3_path.required_with' => 'Foto Produk 3 wajib diunggah jika Nama Produk 3 diisi.',
            'foto_produk_4_path.required_with' => 'Foto Produk 4 wajib diunggah jika Nama Produk 4 diisi.',
            'foto_produk_5_path.required_with' => 'Foto Produk 5 wajib diunggah jika Nama Produk 5 diisi.',
        ];
    }
}
