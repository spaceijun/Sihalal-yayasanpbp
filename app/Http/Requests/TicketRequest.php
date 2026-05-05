<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'     => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'file'        => [
                'nullable',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required'     => 'Subject wajib diisi.',
            'subject.max'          => 'Subject maksimal 255 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'file.max'             => 'Ukuran file maksimal 5 MB.',
            'file.mimes'           => 'Format file tidak didukung. Gunakan: gambar (jpg, png, jpeg, pdf).',
        ];
    }
}
