<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KtpVerifikasiSession extends Model
{
    protected $fillable = [
        'session_key',
        'user_id',
        'batch_id',
        'ktp_count',
        'total_photos',
        'processed',
        'status',
        'ktp_nama',
        'ktp_nik',
        'ktp_url',
        'results',
        'error_message',
    ];

    protected $casts = [
        'results' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Persentase progress 0–100
     */
    public function getProgressPercent(): int
    {
        if ($this->total_photos <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->processed / $this->total_photos) * 100));
    }
}
