<?php

namespace App\Models\Superadmin;

use App\Models\DataEntry;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\User;
use App\Traits\HasHashedId;
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
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'nama_lengkap', 'email', 'telephone', 'fee_enum', 'alamat', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enumerators()
    {
        return $this->hasMany(Enumerator::class, 'id', 'koordinator_id');
    }

    /**
     * Get the associated data_lapangans model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function dataLapangans()
    {
        return $this->hasManyThrough(
            DataLapangan::class,  // Model tujuan
            Enumerator::class,    // Model perantara
            'koordinator_id',     // FK di tabel enumerators
            'enumerator_id',      // FK di tabel data_lapangans
            'id',                 // PK di tabel koordinators
            'id'                  // PK di tabel enumerators
        );
    }
    /**
     * Get the associated data entry models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function dataEntrys()
    {
        return $this->belongsToMany(DataEntry::class, 'data_entry_koordinator', 'koordinator_id', 'data_entry_id');
    }
}

