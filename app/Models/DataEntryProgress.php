<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataEntryProgress extends Model
{
    protected $table = 'data_entry_progress';

    protected $fillable = [
        'user_id',
        'data_entry_id',
        'data_lapangan_id',
        'action',
        'old_data',
        'new_data',
        'keterangan_revisi',
        'keterangan_update',
        'status',
        'actioned_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'actioned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataEntry()
    {
        return $this->belongsTo(DataEntry::class, 'data_entry_id');
    }

    public function dataLapangan()
    {
        return $this->belongsTo(DataLapangan::class, 'data_lapangan_id');
    }

    /**
     * Get the associated penagihan details models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penagihanDetails()
    {
        return $this->hasMany(DataEntryPenagihanDetail::class, 'data_entry_progress_id');
    }
}
