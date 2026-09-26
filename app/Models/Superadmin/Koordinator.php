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
 * @property $foto_ktp
 * @property $foto_formal
 * @property $provinsi_ktp
 * @property $kabupaten_ktp
 * @property $kecamatan_ktp
 * @property $desa_ktp
 * @property $rt_ktp
 * @property $rw_ktp
 * @property $alamat_lengkap_ktp
 * @property $tipe_wilayah_kerja
 * @property $provinsi_kerja
 * @property $kabupaten_kerja
 * @property $tanggal_mulai
 * @property $status
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @property Enumerator[] $enumerators
 * @property-read FeeEnumerator|null $feeAktif
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
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'email',
        'telephone',
        'foto_ktp',
        'foto_formal',
        'provinsi_ktp',
        'kabupaten_ktp',
        'kecamatan_ktp',
        'desa_ktp',
        'rt_ktp',
        'rw_ktp',
        'alamat_lengkap_ktp',
        'tipe_wilayah_kerja',
        'provinsi_kerja',
        'kabupaten_kerja',
        'tanggal_mulai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
    ];

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
        return $this->hasMany(Enumerator::class, 'koordinator_id', 'id');
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

    /**
     * Resolve fee aktif berdasarkan wilayah kerja koordinator ini
     * (Provinsi override > Global fallback). Lihat FeeEnumeratorService.
     */
    public function getFeeAktifAttribute(): ?FeeEnumerator
    {
        return app(\App\Services\Superadmin\FeeEnumeratorService::class)->resolveForKoordinator($this);
    }
}
