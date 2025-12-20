<?php

namespace App\Models\Superadmin;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Koordinator
 *
 * @property $id
 * @property $user_id
 * @property $nama_lengkap
 * @property $email
 * @property $telephone
 * @property $alamat
 * @property $status
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @property Enumerator[] $enumerators
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Koordinator extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'nama_lengkap', 'email', 'telephone', 'alamat', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Superadmin\User::class, 'user_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enumerators()
    {
        return $this->hasMany(\App\Models\Superadmin\Enumerator::class, 'id', 'koordinator_id');
    }
    
}
