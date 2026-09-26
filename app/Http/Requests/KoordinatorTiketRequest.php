<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KoordinatorTiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required', 'in:Data Lapangan,Enumerator,Teknis Lapangan,Lainnya'],
            'ref_data_lapangan_id' => [
                Rule::requiredIf(fn () => $this->input('kategori') === 'Data Lapangan'),
                'nullable', 'exists:data_lapangans,id',
            ],
            'ref_enumerator_id' => [
                Rule::requiredIf(fn () => $this->input('kategori') === 'Enumerator'),
                'nullable', 'exists:enumerators,id',
            ],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.required' => 'Kategori wajib dipilih',
            'ref_data_lapangan_id.required_if' => 'Data lapangan terkait wajib dipilih',
            'ref_enumerator_id.required_if' => 'Enumerator terkait wajib dipilih',
            'subject.required' => 'Subjek wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'file.max' => 'Ukuran file maksimal 2 MB',
            'file.mimes' => 'Format file harus jpg, png, atau pdf',
        ];
    }
}
