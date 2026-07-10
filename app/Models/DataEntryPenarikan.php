<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataEntryPenarikan extends Model
{
    protected $table = 'data_entry_penarikan';

    protected $fillable = [
        'user_id',
        'data_entry_id',
        'nominal',
        'catatan_de',
        'catatan_admin',
        'status',
        'receipt_path',
        'tanggal_pengajuan',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_diproses' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataEntry()
    {
        return $this->belongsTo(DataEntry::class, 'data_entry_id');
    }

    /**
     * Penagihan-penagihan yang dicakup penarikan ini.
     */
    public function penagihans()
    {
        return $this->belongsToMany(
            DataEntryPenagihan::class,
            'data_entry_penarikan_penagihan',
            'penarikan_id',
            'penagihan_id'
        );
    }
}
