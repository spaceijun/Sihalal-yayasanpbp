<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpotcheckRequest extends FormRequest
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
            'data_lapangan_id' => 'required',
            'nama_spotcheck' => 'string',
            'tanggal_spotcheck' => 'required|date',
            'foto_pu' => 'required|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'hasil_spotcheck' => 'string',
        ];
    }
}
