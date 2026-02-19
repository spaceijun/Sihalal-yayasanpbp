<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DataEntry
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
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DataEntry extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'data_entrys';
    protected $fillable = ['user_id', 'nama_lengkap', 'email', 'telephone', 'alamat', 'status', 'entry_type'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * Get the associated data entry progress models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progress()
    {
        return $this->hasMany(DataEntryProgress::class, 'data_entry_id');
    }
}
