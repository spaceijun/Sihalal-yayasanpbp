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
            'recruit_type'       => 'required|in:PENDAMPING,DATA ENTRY',
            'type_entry'         => 'required_if:recruit_type,DATA ENTRY|nullable|string',
            'nama_lengkap'        => 'required|string',
            'nik'                 => 'required|digits:16|unique:recruitments,nik',
            'jenis_kelamin'       => 'required|in:Laki-laki,Perempuan',
            'telephone'           => 'required|string',
            'alamat_lengkap'      => 'required|string',
            'pengalaman'          => 'required|string',
            'rekomendasi'         => 'nullable|string',
            'pendidikan_terakhir' => 'required|string',
            'foto_diri'           => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'foto_ktp'            => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'foto_ijasah'         => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'pakta_integritas'    => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'status'              => 'required',
            'alasan_penolakan'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'recruit_type.required'       => 'Tipe Rekrutmen wajib dipilih.',
            'recruit_type.in'             => 'Tipe Rekrutmen tidak valid.',
            'type_entry.required_if'      => 'Tipe Rekrutmen DATA ENTRY wajib dipilih.',
            'nama_lengkap.required'        => 'Nama Lengkap wajib diisi.',
            'nik.required'                 => 'NIK wajib diisi.',
            'nik.digits'                   => 'NIK harus 16 digit angka.',
            'nik.unique'                   => 'NIK sudah terdaftar, hubungi admin jika terjadi kesalahan.',
            'jenis_kelamin.required'       => 'Jenis Kelamin wajib dipilih.',
            'jenis_kelamin.in'             => 'Jenis Kelamin tidak valid.',
            'telephone.required'           => 'Nomor Telepon wajib diisi.',
            'alamat_lengkap.required'      => 'Alamat Lengkap wajib diisi.',
            'pengalaman.required'          => 'Pengalaman wajib diisi.',
            'pendidikan_terakhir.required' => 'Pendidikan Terakhir wajib diisi.',
            'foto_diri.required'           => 'Foto Diri wajib diunggah.',
            'foto_diri.mimes'              => 'Format Foto Diri harus JPEG, PNG, JPG, GIF, atau SVG.',
            'foto_diri.max'                => 'Ukuran Foto Diri maksimal 10MB.',
            'foto_ktp.required'            => 'Foto KTP wajib diunggah.',
            'foto_ktp.mimes'               => 'Format Foto KTP harus JPEG, PNG, JPG, GIF, atau SVG.',
            'foto_ktp.max'                 => 'Ukuran Foto KTP maksimal 10MB.',
            'foto_ijasah.required'         => 'Foto Ijazah wajib diunggah.',
            'foto_ijasah.mimes'            => 'Format Foto Ijazah harus JPEG, PNG, JPG, atau PDF.',
            'foto_ijasah.max'              => 'Ukuran Foto Ijazah maksimal 10MB.',
            'pakta_integritas.required'    => 'Pakta Integritas wajib diunggah.',
            'pakta_integritas.mimes'       => 'Format Pakta Integritas harus JPEG, PNG, JPG, atau PDF.',
            'pakta_integritas.max'         => 'Ukuran Pakta Integritas maksimal 10MB.',
            'status.required'              => 'Status wajib diisi.',
        ];
    }
}
