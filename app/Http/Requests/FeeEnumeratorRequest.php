<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeeEnumeratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skala' => ['required', 'in:Global,Provinsi'],
            'provinsi' => [
                Rule::requiredIf(fn () => $this->input('skala') === 'Provinsi'),
                'nullable', 'string',
            ],
            'tipe_fee' => ['required', 'in:Bulan,Target'],
            'target_data' => [
                Rule::requiredIf(fn () => $this->input('tipe_fee') === 'Target'),
                'nullable', 'integer', 'min:1',
            ],
            'nominal_fee' => ['required', 'integer', 'min:0'],
            'is_aktif' => ['nullable', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'skala.required' => 'Skala wajib dipilih',
            'provinsi.required_if' => 'Provinsi wajib dipilih untuk skala Provinsi',
            'tipe_fee.required' => 'Tipe fee wajib dipilih',
            'target_data.required_if' => 'Target data wajib diisi untuk tipe fee Target',
            'nominal_fee.required' => 'Nominal fee wajib diisi',
        ];
    }
}
