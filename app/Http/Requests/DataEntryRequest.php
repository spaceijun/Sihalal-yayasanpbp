<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataEntryRequest extends FormRequest
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
            'entry_type' => 'required',
        ];
    }
}
