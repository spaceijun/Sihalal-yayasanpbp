<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    protected $fillable = [
        'user_id',
        'no_ticket',
        'subject',
        'description',
        'file',
        'status',
        'kategori',
        'ref_data_lapangan_id',
        'ref_enumerator_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dataLapangan()
    {
        return $this->belongsTo(DataLapangan::class, 'ref_data_lapangan_id');
    }

    public function enumerator()
    {
        return $this->belongsTo(Enumerator::class, 'ref_enumerator_id');
    }
}
