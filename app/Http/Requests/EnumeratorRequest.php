<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnumeratorRequest extends FormRequest
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
            'koordinator_id' => 'required',
            'nama_lengkap' => 'required|string',
            'telephone' => 'required|string',
            'foto_diri' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            // 'no_registrasi' => 'required|string|unique:enumerators,no_registrasi',
            'alamat' => 'required|string',
            'status' => 'required',
        ];
    }
    /**
     * Mengembalikan pesan error untuk validasi form.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'koordinator_id.required' => 'Koordinator wajib diisi',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'telephone.required' => 'Nomor Telepon wajib diisi',
            'foto_diri.required' => 'Foto Diri wajib diunggah',
            // 'no_registrasi.required' => 'Nomor Registrasi wajib diisi',
            // 'no_registrasi.unique' => 'Nomor Registrasi sudah digunakan',
            'alamat.required' => 'Alamat wajib diisi',
            'status.required' => 'Status wajib diisi',
        ];
    }
}
