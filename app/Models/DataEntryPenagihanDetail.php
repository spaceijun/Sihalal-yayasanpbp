<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataEntryPenagihanDetail extends Model
{
    protected $table = 'data_entry_penagihan_details';

    protected $fillable = [
        'penagihan_id',
        'data_entry_progress_id',
    ];

    public function penagihan()
    {
        return $this->belongsTo(DataEntryPenagihan::class, 'penagihan_id');
    }

    public function dataEntryProgress()
    {
        return $this->belongsTo(DataEntryProgress::class, 'data_entry_progress_id');
    }
}
