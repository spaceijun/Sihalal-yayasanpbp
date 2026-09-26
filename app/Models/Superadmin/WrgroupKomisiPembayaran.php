<?php

namespace App\Models\Superadmin;

use App\Models\User;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat lokal pengajuan bukti setoran komisi ke WRGROUP (lihat .agent/workflows/wrgroup-integrasi.md).
 * Bersifat sinkron — bukan bagian dari wrgroup_outbox — sekali kirim per baris, dengan tombol
 * "Coba Lagi" manual bila gagal (bukan retry otomatis terjadwal seperti event invoice/payment).
 */
class WrgroupKomisiPembayaran extends Model
{
    use HasHashedId;

    protected $table = 'wrgroup_komisi_pembayarans';

    protected $fillable = [
        'event_id',
        'komisi_reference',
        'periode',
        'jumlah',
        'tanggal_transaksi_bank',
        'referensi_bank',
        'catatan',
        'bukti_setoran',
        'berhasil',
        'http_status',
        'response_message',
        'dikirim_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_transaksi_bank' => 'date',
            'berhasil' => 'boolean',
            'http_status' => 'integer',
        ];
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikirim_oleh');
    }
}
