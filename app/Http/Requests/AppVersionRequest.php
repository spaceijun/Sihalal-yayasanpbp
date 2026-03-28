<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppVersionRequest extends FormRequest
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
			'version' => 'required|string',
			'build_number' => 'required',
			'changelog' => 'string',
			'force_update' => 'required',
			'download_url' => 'required|string',
        ];
    }
}
