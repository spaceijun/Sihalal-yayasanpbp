<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Spotcheck
 *
 * @property $id
 * @property $data_lapangan_id
 * @property $nama_spotcheck
 * @property $tanggal_spotcheck
 * @property $foto_pu
 * @property $hasil_spotcheck
 * @property $created_at
 * @property $updated_at
 *
 * @property DataLapangan $dataLapangan
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Spotcheck extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['enumerator_id', 'data_lapangan_id', 'nama_spotcheck', 'tanggal_spotcheck', 'foto_pu', 'hasil_spotcheck'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dataLapangan()
    {
        return $this->belongsTo(\App\Models\DataLapangan::class, 'data_lapangan_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * Get the associated Enumerator model.
     *
     * @see \App\Models\Enumerator
     */
    public function enumerator()
    {
        return $this->belongsTo(\App\Models\Enumerator::class, 'enumerator_id', 'id');
    }
}

