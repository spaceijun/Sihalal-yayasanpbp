<?php

namespace App\Models\Superadmin;

use App\Models\User;
use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Arsip lokal surat yang diajukan ke WRGROUP lewat modul Pembuat Surat mereka (lihat
 * .agent/workflows/wrgroup-integrasi.md). Template/nomor/PDF tetap 100% dikuasai & dirender
 * WRGROUP — baris ini hanya salinan status + PDF terbit utk ditelusuri dari sini.
 */
class WrgroupSurat extends Model
{
    use HasHashedId;

    protected $fillable = [
        'wrgroup_hashed_id',
        'surat_jenis_kode',
        'jenis_nama',
        'perihal',
        'data_variabel',
        'status',
        'nomor',
        'diterbitkan_at',
        'catatan_terakhir',
        'pdf_path',
        'berhasil_kirim',
        'http_status',
        'response_message',
        'diajukan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'data_variabel' => 'array',
            'diterbitkan_at' => 'datetime',
            'berhasil_kirim' => 'boolean',
            'http_status' => 'integer',
        ];
    }

    public const STATUS_LABEL = [
        'diajukan' => 'Diajukan',
        'diperiksa' => 'Diperiksa',
        'disetujui' => 'Disetujui',
        'terbit' => 'Terbit',
        'dibatalkan' => 'Dibatalkan/Ditolak',
    ];

    public const STATUS_BADGE = [
        'diajukan' => 'adm-badge-pending',
        'diperiksa' => 'adm-badge-pending',
        'disetujui' => 'adm-badge-info',
        'terbit' => 'adm-badge-success',
        'dibatalkan' => 'adm-badge-danger',
    ];

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->berhasil_kirim) {
            return 'Gagal Dikirim';
        }

        return self::STATUS_LABEL[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if (! $this->berhasil_kirim) {
            return 'adm-badge-danger';
        }

        return self::STATUS_BADGE[$this->status] ?? 'adm-badge-info';
    }
}
