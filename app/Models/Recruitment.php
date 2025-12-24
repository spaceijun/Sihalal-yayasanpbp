<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Recruitment
 *
 * @property $id
 * @property $koordinator_id
 * @property $nama_lengkap
 * @property $telephone
 * @property $alamat_lengkap
 * @property $pengalaman
 * @property $rekomendasi
 * @property $pendidikan_terakhir
 * @property $foto_diri
 * @property $foto_ktp
 * @property $status
 * @property $alasan_penolakan
 * @property $created_at
 * @property $updated_at
 *
 * @property Koordinator $koordinator
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Recruitment extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['koordinator_id', 'nama_lengkap', 'telephone', 'alamat_lengkap', 'pengalaman', 'rekomendasi', 'pendidikan_terakhir', 'foto_diri', 'foto_ktp', 'status', 'alasan_penolakan'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function koordinator()
    {
        return $this->belongsTo(\App\Models\Superadmin\Koordinator::class, 'koordinator_id', 'id');
    }
}
