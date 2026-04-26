<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Verifikator
 *
 * @property $id
 * @property $nama_lengkap
 * @property $telephone
 * @property $alamat_lengkap
 * @property $rate_per_data
 * @property $created_at
 * @property $updated_at
 *
 * @property DataLapangan[] $dataLapangans
 * @property VerifikatorPayment[] $verifikatorPayments
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Verifikator extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nama_lengkap', 'telephone', 'alamat_lengkap', 'rate_per_data'];


    /**
     * Get the associated dataLapangan models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dataLapangans()
    {
        return $this->hasMany(DataLapangan::class);
    }

    // Karena pakai withCount di controller, tinggal hitung nominal
    public function getTotalNominalAttribute(): float
    {
        $jumlah = $this->jumlah_belum_dibayar ?? $this->dataLapangans()->whereNull('payment_id')->count();
        return $jumlah * $this->rate_per_data;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifikatorPayments()
    {
        return $this->hasMany(\App\Models\VerifikatorPayment::class);
    }

    // Kalkulasi aktif (belum dibayar)
    public function belumDibayar()
    {
        return $this->hasMany(DataLapangan::class)->whereNull('payment_id');
    }

    public function getJumlahBelumDibayarAttribute(): int
    {
        return $this->belumDibayar()->count();
    }
}

