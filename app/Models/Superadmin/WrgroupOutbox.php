<?php

namespace App\Models\Superadmin;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Outbox integrasi WRGROUP Super Apps (lihat .agent/workflows/wrgroup-integrasi.md).
 */
class WrgroupOutbox extends Model
{
    use HasHashedId;

    protected $table = 'wrgroup_outbox';

    protected $fillable = [
        'event_id',
        'endpoint',
        'transaction_id',
        'version',
        'type',
        'period',
        'source_type',
        'source_id',
        'payload',
        'payload_hash',
        'status',
        'attempts',
        'next_attempt_at',
        'locked_at',
        'last_http_status',
        'last_response',
        'last_error',
        'environment',
        'persisted',
        'event_time',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'attempts' => 'integer',
            'payload' => 'array',
            'last_response' => 'array',
            'persisted' => 'boolean',
            'next_attempt_at' => 'datetime',
            'locked_at' => 'datetime',
            'event_time' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public const ENDPOINT_LABEL = [
        'invoice' => 'Invoice',
        'refund' => 'Refund',
        'payment' => 'Pembayaran',
        'nihil' => 'Laporan Nihil',
    ];

    public const STATUS_LABEL = [
        'pending' => 'Menunggu',
        'sent' => 'Terkirim',
        'rejected' => 'Ditolak',
        'failed' => 'Gagal',
    ];

    /**
     * @var array<string, string>
     */
    public const STATUS_BADGE = [
        'pending' => 'adm-badge-pending',
        'sent' => 'adm-badge-success',
        'rejected' => 'adm-badge-danger',
        'failed' => 'adm-badge-danger',
    ];

    /** Event pending yang sudah waktunya dikirim. */
    public function scopeDue(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where(fn (Builder $q) => $q->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now()));
    }

    /** Perlu tindakan manual (kirim ulang setelah data/kredensial diperbaiki). */
    public function isRetryable(): bool
    {
        return in_array($this->status, ['rejected', 'failed'], true);
    }

    public function getEndpointLabelAttribute(): string
    {
        return self::ENDPOINT_LABEL[$this->endpoint] ?? ucfirst((string) $this->endpoint);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::STATUS_BADGE[$this->status] ?? 'adm-badge-info';
    }
}
