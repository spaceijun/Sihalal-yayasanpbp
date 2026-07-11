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
    protected $fillable = ['user_id', 'koordinator_id', 'nama_lengkap', 'telephone', 'foto_diri', 'no_registrasi', 'alamat', 'status', 'bank_id', 'no_rekening', 'nama_rekening', 'last_pengajuan_at'];

    protected $casts = [
        'last_pengajuan_at' => 'datetime',
    ];

    /**
     * Cek apakah enumerator boleh melakukan pengajuan tarik saldo.
     * Syarat: status Aktif & last_pengajuan_at > 7 hari yang lalu (atau belum pernah ajukan).
     */
    public function bisaAjukan(): bool
    {
        if ($this->status !== 'Aktif') {
            return false;
        }

        if (! $this->last_pengajuan_at) {
            return true;
        }

        return $this->last_pengajuan_at->diffInDays(now()) >= 7;
    }

    /**
     * Sisa hari cooldown sebelum enumerator bisa ajukan lagi.
     * Mengembalikan 0 jika sudah bisa ajukan.
     */
    public function sisaHariCooldown(): int
    {
        if (! $this->last_pengajuan_at) {
            return 0;
        }

        $selisih = $this->last_pengajuan_at->diffInDays(now());

        return $selisih >= 7 ? 0 : (int) (7 - $selisih);
    }


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
     * @return HasMany
     */
    public function aktivasiLogs(): HasMany
    {
        return $this->hasMany(EnumeratorAktivasiLog::class, 'enumerator_id', 'id')
            ->latest('tanggal_aktivasi');
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
