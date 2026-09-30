<?php

namespace App\Models\Superadmin;

use App\Models\Cashflow;
use App\Models\User;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat lokal pengajuan bukti setoran komisi ke WRGROUP (lihat .agent/workflows/wrgroup-integrasi.md).
 * Bersifat sinkron — bukan bagian dari wrgroup_outbox — sekali kirim per baris, dengan tombol
 * "Coba Lagi" manual bila gagal (bukan retry otomatis terjadwal seperti event invoice/payment).
 * Status verifikasi WRGROUP disinkronkan terjadwal (wrgroup:komisi-sync); setoran terverifikasi
 * dibukukan sekali sebagai Pengeluaran di Arus Kas.
 */
class WrgroupKomisiPembayaran extends Model
{
    use HasHashedId;

    public const STATUS_FINAL = ['terverifikasi', 'ditolak'];

    public const STATUS_VERIFIKASI_LABEL = [
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'terverifikasi' => 'Terverifikasi',
        'ditolak' => 'Ditolak',
    ];

    public const STATUS_VERIFIKASI_BADGE = [
        'menunggu_verifikasi' => 'adm-badge-pending',
        'terverifikasi' => 'adm-badge-success',
        'ditolak' => 'adm-badge-danger',
    ];

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
        'status_verifikasi',
        'diverifikasi_at',
        'catatan_verifikasi',
        'cashflow_id',
        'dibukukan_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_transaksi_bank' => 'date',
            'berhasil' => 'boolean',
            'http_status' => 'integer',
            'diverifikasi_at' => 'datetime',
            'dibukukan_at' => 'datetime',
        ];
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikirim_oleh');
    }

    public function cashflow(): BelongsTo
    {
        return $this->belongsTo(Cashflow::class);
    }

    public function getStatusVerifikasiLabelAttribute(): string
    {
        return self::STATUS_VERIFIKASI_LABEL[$this->status_verifikasi] ?? 'Belum tersinkron';
    }

    public function getStatusVerifikasiBadgeClassAttribute(): string
    {
        return self::STATUS_VERIFIKASI_BADGE[$this->status_verifikasi] ?? 'adm-badge-info';
    }
}
