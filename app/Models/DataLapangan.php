<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DataLapangan
 *
 * @property $id
 * @property $enumerator_id
 * @property $nama_pu
 * @property $nik
 * @property $rt
 * @property $rw
 * @property $alamat
 * @property $titik_koordinat
 * @property $foto_ktp
 * @property $foto_rumah
 * @property $foto_pendamping
 * @property $foto_proses
 * @property $foto_produk
 * @property $created_at
 * @property $updated_at
 *
 * @property Enumerator $enumerator
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DataLapangan extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['enumerator_id', 'nama_pu', 'nik', 'rt', 'rw', 'alamat', 'titik_koordinat', 'foto_ktp', 'foto_rumah', 'foto_pendamping', 'foto_proses', 'foto_produk'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enumerator()
    {
        return $this->belongsTo(\App\Models\Enumerator::class, 'enumerator_id', 'id');
    }
    
}
