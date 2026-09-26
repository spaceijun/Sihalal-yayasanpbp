<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WrgroupNihilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dijaga middleware role:superadmin pada route group
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'periode' => ['required', 'string', 'regex:/^\d{4}-Q[1-4]$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'periode.regex' => 'Format periode harus YYYY-Qn, mis. 2026-Q2.',
        ];
    }
}
