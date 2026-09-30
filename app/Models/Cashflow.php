<?php

namespace App\Models;

use App\Traits\HasHashedId;
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
    use HasHashedId;

    /** Setoran komisi ke WRGROUP yang sudah diverifikasi — dikecualikan dari dasar komisi (CashflowService::netPeriode()). */
    public const SUMBER_KOMISI_WRGROUP = 'komisi_wrgroup';

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['data_lapangan_id', 'tipe', 'sumber', 'jumlah', 'keterangan', 'tanggal'];
}
