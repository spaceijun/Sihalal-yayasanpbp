<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            // 'user_id' => 'required',
            'nama_lengkap' => 'required|string',
            'email' => 'required|string',
            'telephone' => 'required|string',
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
            // 'user_id.required' => 'User ID wajib diisi',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'telephone.required' => 'Nomor Telepon wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'status.required' => 'Status wajib diisi',
        ];
    }
}
