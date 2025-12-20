<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cashflow
 *
 * @property $id
 * @property $tipe
 * @property $jumlah
 * @property $keterangan
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cashflow extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['data_lapangan_id', 'tipe', 'jumlah', 'keterangan', 'tanggal'];
}
