<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnumeratorAktivasiLog extends Model
{
    protected $fillable = [
        'enumerator_id',
        'diaktifkan_oleh',
        'surat_pernyataan',
        'catatan',
        'tanggal_aktivasi',
    ];

    protected $casts = [
        'tanggal_aktivasi' => 'datetime',
    ];

    public function enumerator(): BelongsTo
    {
        return $this->belongsTo(Enumerator::class);
    }
}
