<?php

namespace App\Models\Superadmin;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

class FeeEnumerator extends Model
{
    use HasHashedId;

    protected $table = 'fee_enumerators';

    protected $fillable = [
        'skala',
        'provinsi',
        'tipe_fee',
        'target_data',
        'nominal_fee',
        'is_aktif',
        'keterangan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'nominal_fee' => 'integer',
        'target_data' => 'integer',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopeGlobal($query)
    {
        return $query->where('skala', 'Global');
    }

    public function scopeProvinsi($query, string $provinsi)
    {
        return $query->where('skala', 'Provinsi')->where('provinsi', $provinsi);
    }
}
