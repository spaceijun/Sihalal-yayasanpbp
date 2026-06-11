<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

class TicketPendamping extends Model
{
    use HasHashedId;

    protected $table = 'ticket_pendampings';
    protected $perPage = 20;

    protected $fillable = [
        'user_id',
        'data_lapangan_id',
        'no_tiket',
        'isi_kendala',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dataLapangan()
    {
        return $this->belongsTo(DataLapangan::class, 'data_lapangan_id');
    }
}
