<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pengumuman
 *
 * @property $id
 * @property $nomor
 * @property $judul
 * @property $jenis
 * @property $foto
 * @property $deskripsi
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Pengumuman extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pengumumans';
    protected $fillable = ['nomor', 'judul', 'jenis', 'foto', 'deskripsi'];
}
