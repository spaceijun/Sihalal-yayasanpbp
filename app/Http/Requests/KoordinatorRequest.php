<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KoordinatorRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $koordinatorId = $this->route('koordinator')?->id;

        return [
            // Akun Login
            'email' => ['required', 'email', Rule::unique('koordinators')->ignore($koordinatorId)],
            'password' => $isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],

            // Data Pribadi
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'foto_ktp' => $isUpdate ? ['nullable', 'image', 'max:2048'] : ['required', 'image', 'max:2048'],
            'foto_formal' => $isUpdate ? ['nullable', 'image', 'max:2048'] : ['required', 'image', 'max:2048'],

            // Alamat Sesuai KTP
            'provinsi_ktp' => ['required', 'string'],
            'kabupaten_ktp' => ['required', 'string'],
            'kecamatan_ktp' => ['required', 'string'],
            'desa_ktp' => ['required', 'string'],
            'rt_ktp' => ['required', 'string', 'max:5'],
            'rw_ktp' => ['required', 'string', 'max:5'],
            'alamat_lengkap_ktp' => ['required', 'string'],

            // Wilayah Kerja
            'tipe_wilayah_kerja' => ['required', 'in:Provinsi,Kabupaten'],
            'provinsi_kerja' => ['required', 'string'],
            'kabupaten_kerja' => [
                Rule::requiredIf(fn () => $this->input('tipe_wilayah_kerja') === 'Kabupaten'),
                'nullable', 'string',
            ],

            // Informasi Lainnya
            'tanggal_mulai' => ['required', 'date'],
            'status' => ['required', 'in:Aktif,Tidak Aktif,Blacklist'],
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
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah digunakan koordinator lain',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'telephone.required' => 'Nomor Telepon wajib diisi',
            'foto_ktp.required' => 'Foto KTP wajib diunggah',
            'foto_ktp.image' => 'Foto KTP harus berupa gambar',
            'foto_formal.required' => 'Foto Formal wajib diunggah',
            'foto_formal.image' => 'Foto Formal harus berupa gambar',
            'provinsi_ktp.required' => 'Provinsi wajib dipilih',
            'kabupaten_ktp.required' => 'Kabupaten wajib dipilih',
            'kecamatan_ktp.required' => 'Kecamatan wajib dipilih',
            'desa_ktp.required' => 'Desa/Kelurahan wajib dipilih',
            'rt_ktp.required' => 'RT wajib diisi',
            'rw_ktp.required' => 'RW wajib diisi',
            'alamat_lengkap_ktp.required' => 'Alamat lengkap wajib diisi',
            'tipe_wilayah_kerja.required' => 'Tipe wilayah kerja wajib dipilih',
            'provinsi_kerja.required' => 'Provinsi wilayah kerja wajib dipilih',
            'kabupaten_kerja.required_if' => 'Kabupaten wilayah kerja wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'status.required' => 'Status wajib diisi',
        ];
    }
}
