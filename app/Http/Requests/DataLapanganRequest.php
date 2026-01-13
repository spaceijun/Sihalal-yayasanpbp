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
            'enumerator_id' => 'required',
            'nama_pu' => 'required|string',
            'nik' => 'required|digits:16|unique:data_lapangans,nik',
            'telephone' => 'required|string',
            'nama_produk' => 'required|string',
            'alamat' => 'required|string',
            'titik_koordinat' => 'required|string',
            'foto_ktp_path' => 'required|string',
            'foto_rumah_path' => 'required|string',
            'foto_pendamping_path' => 'required|string',
            'foto_proses_path' => 'required|string',
            'foto_produk_path' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'enumerator_id.required' => 'Enumerator wajib diisi.',
            'nama_pu.required' => 'Nama PU wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'telephone.required' => 'Nomor Telepon wajib diisi.',
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'titik_koordinat.required' => 'Titik Koordinat wajib diisi.',
            'foto_ktp_path.required' => 'Foto KTP wajib diunggah.',
            'foto_rumah_path.required' => 'Foto Rumah wajib diunggah.',
            'foto_pendamping_path.required' => 'Foto Pendamping wajib diunggah.',
            'foto_proses_path.required' => 'Foto Proses wajib diunggah.',
            'foto_produk_path.required' => 'Foto Produk wajib diunggah.',
        ];
    }
}
