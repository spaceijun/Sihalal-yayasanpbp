<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['koordinator_id', 'nama_lengkap', 'telephone', 'alamat', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function koordinator()
    {
        return $this->belongsTo(\App\Models\Superadmin\Koordinator::class, 'koordinator_id', 'id');
    }
}
