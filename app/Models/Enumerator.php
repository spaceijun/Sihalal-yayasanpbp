<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Enumerator
 *
 * @property $id
 * @property $koordinator_id
 * @property $nama_lengkap
 * @property $telephone
 * @property $alamat
 * @property $status
 * @property $created_at
 * @property $updated_at
 *
 * @property Koordinator $koordinator
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Enumerator extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'koordinator_id', 'nama_lengkap', 'telephone', 'foto_diri', 'no_registrasi', 'alamat', 'status', 'bank_id', 'no_rekening', 'nama_rekening'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function koordinator()
    {
        return $this->belongsTo(\App\Models\Superadmin\Koordinator::class, 'koordinator_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function dataLapangans(): HasMany
    {
        return $this->hasMany(DataLapangan::class, 'enumerator_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo(DataBank::class, 'bank_id', 'id');
    }

    /**
     * Get the associated User model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
