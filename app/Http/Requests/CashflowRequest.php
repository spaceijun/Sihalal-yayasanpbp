<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashflowRequest extends FormRequest
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
            'tipe' => 'required',
            'jumlah' => 'required',
            'keterangan' => 'required|string',
            'tanggal' => 'required|date',
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
            'tipe.required' => 'Tipe wajib diisi',
            'jumlah.required' => 'Jumlah wajib diisi',
            'keterangan.required' => 'Keterangan wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
        ];
    }
}
