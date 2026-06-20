<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashedId;

class DataEntryProgress extends Model
{
    use HasHashedId;

    protected $table = 'data_entry_progress';

    protected $fillable = [
        'user_id',
        'data_entry_id',
        'data_lapangan_id',
        'action',
        'old_data',
        'new_data',
        'status',
        'keterangan_revisi',
        'verifikator_id',
        'tanggal_verifikasi',
        'payment_id',
        'actioned_at',
    ];

    protected $casts = [
        'old_data'           => 'array',
        'new_data'           => 'array',
        'tanggal_verifikasi' => 'date',
        'actioned_at'        => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function dataLapangan()
    {
        return $this->belongsTo(DataLapangan::class, 'data_lapangan_id');
    }

    public function dataEntry()
    {
        return $this->belongsTo(DataEntry::class, 'data_entry_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(Verifikator::class, 'verifikator_id');
    }

    public function payment()
    {
        return $this->belongsTo(VerifikatorPayment::class, 'payment_id');
    }

    /* Get the associated penagihan details models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penagihanDetails()
    {
        return $this->hasMany(DataEntryPenagihanDetail::class, 'data_entry_progress_id');
    }


    /**
     * Scope a query to only include data that has not been paid and is in "DITERIMA" status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBelumDibayar($query)
    {
        return $query->whereNull('payment_id')
            ->where('status', 'DITERIMA');
    }
}
