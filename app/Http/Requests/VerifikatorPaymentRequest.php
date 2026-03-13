<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikatorPaymentRequest extends FormRequest
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
			'verifikator_id' => 'required',
			'jumlah_data' => 'required',
			'total_nominal' => 'required',
			'periode_dari' => 'required',
			'periode_sampai' => 'required',
			'paid_at' => 'required',
        ];
    }
}
