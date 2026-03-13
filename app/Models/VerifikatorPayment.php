<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VerifikatorPayment
 *
 * @property $id
 * @property $verifikator_id
 * @property $jumlah_data
 * @property $total_nominal
 * @property $periode_dari
 * @property $periode_sampai
 * @property $paid_at
 * @property $created_at
 * @property $updated_at
 *
 * @property Verifikator $verifikator
 * @property DataLapangan[] $dataLapangans
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class VerifikatorPayment extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['verifikator_id', 'jumlah_data', 'total_nominal', 'periode_dari', 'periode_sampai', 'paid_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verifikator()
    {
        return $this->belongsTo(\App\Models\Verifikator::class, 'verifikator_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dataLapangans()
    {
        return $this->hasMany(\App\Models\DataLapangan::class, 'payment_id', 'id');
    }
}
