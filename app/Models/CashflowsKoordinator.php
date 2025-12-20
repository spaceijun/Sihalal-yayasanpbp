<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashflowsKoordinator extends Model
{
    protected $fillable = [
        'data_lapangan_id',
        'tipe',
        'nominal',
        'keterangan',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2'
    ];

    public function dataLapangan()
    {
        return $this->belongsTo(DataLapangan::class);
    }
}
