<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AppVersion
 *
 * @property $id
 * @property $version
 * @property $build_number
 * @property $changelog
 * @property $force_update
 * @property $download_url
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AppVersion extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['version', 'build_number', 'changelog', 'force_update', 'download_url'];

    protected $casts = [
        'build_number' => 'integer',
        'force_update' => 'boolean',
    ];
}
