<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecruitmentRequest extends FormRequest
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
            'nama_lengkap' => 'required|string',
            'telephone' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'pengalaman' => 'required|string',
            'rekomendasi' => 'string',
            'pendidikan_terakhir' => 'required|string',
            'foto_diri' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'status' => 'required',
            'alasan_penolakan' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'telephone.required' => 'Nomor Telepon wajib diisi.',
            'alamat_lengkap.required' => 'Alamat Lengkap wajib diisi.',
            'pengalaman.required' => 'Pengalaman wajib diisi.',
            'pendidikan_terakhir.required' => 'Pendidikan Terakhir wajib diisi.',
            'foto_diri.required' => 'Foto Diri wajib diunggah.',
            'foto_ktp.required' => 'Foto KTP wajib diunggah.',
            'status.required' => 'Status wajib diisi.',
        ];
    }
}
