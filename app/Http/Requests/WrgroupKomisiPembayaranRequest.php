<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Rule harus tetap sinkron dengan PilarKomisiPembayaranRequest di WRGROUP
 * (app/Http/Requests/Api/PilarKomisiPembayaranRequest.php) — keduanya memvalidasi bentuk data yang sama.
 */
class WrgroupKomisiPembayaranRequest extends FormRequest
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
            'periode' => 'required|string|max:20',
            'jumlah' => 'required|numeric|gt:0',
            'tanggal_transaksi_bank' => 'required|date',
            'referensi_bank' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
            'bukti_setoran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ];
    }
}
