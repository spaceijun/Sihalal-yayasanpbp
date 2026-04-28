<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ResepMakanan
 *
 * @property $id
 * @property $nama_produk
 * @property $kategori
 * @property $foto
 * @property $bahan_makanan
 * @property $proses_pembuatan
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ResepMakanan extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    protected $appends = ['hashed_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nama_produk', 'kategori', 'foto', 'bahan_makanan', 'proses_pembuatan'];
}
