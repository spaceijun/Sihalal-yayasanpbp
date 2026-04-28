<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResepMakananRequest extends FormRequest
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
            'nama_produk' => 'required|string',
            'kategori' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bahan_makanan' => 'required|string',
            'proses_pembuatan' => 'required|string',
        ];
    }
}
