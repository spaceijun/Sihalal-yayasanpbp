<?php

namespace App\Models;

use App\Traits\SendsWhatsAppNotification;
use Illuminate\Database\Eloquent\Model;

class DataEntryPenagihan extends Model
{
    use SendsWhatsAppNotification;

    protected $table = 'data_entry_penagihans';

    protected $fillable = [
        'user_id',
        'data_entry_id',
        'jumlah_data',
        'jumlah_paket',
        'nominal',
        'status',
        'catatan',
        'tanggal_tagihan',
        'tanggal_dibayar',
        'receipt_path',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'datetime',
        'tanggal_dibayar' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataEntry()
    {
        return $this->belongsTo(DataEntry::class, 'data_entry_id');
    }

    public function details()
    {
        return $this->hasMany(DataEntryPenagihanDetail::class);
    }
}
