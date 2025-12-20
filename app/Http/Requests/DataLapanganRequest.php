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
            'nik' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'alamat' => 'required|string',
            'titik_koordinat' => 'required|string',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'foto_rumah' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'foto_pendamping' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'foto_proses' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'foto_produk' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'enumerator_id.required' => 'Enumerator wajib diisi.',
            'nama_pu.required' => 'Nama PU wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'rt.required' => 'RT wajib diisi.',
            'rw.required' => 'RW wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'titik_koordinat.required' => 'Titik Koordinat wajib diisi.',
            'foto_ktp.required' => 'Foto KTP wajib diunggah.',
            'foto_rumah.required' => 'Foto Rumah wajib diunggah.',
            'foto_pendamping.required' => 'Foto Pendamping wajib diunggah.',
            'foto_proses.required' => 'Foto Proses wajib diunggah.',
            'foto_produk.required' => 'Foto Produk wajib diunggah.',
        ];
    }
}
