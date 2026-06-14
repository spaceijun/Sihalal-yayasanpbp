<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RecruitmentPost
 *
 * @property $id
 * @property $nama_loker
 * @property $posisi
 * @property $deskripsi
 * @property $jobdesk
 * @property $is_active
 * @property $slug
 * @property $requirements
 * @property $tanggal_buka
 * @property $tanggal_tutup
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class RecruitmentPost extends Model
{
    use HasHashedId;

    protected $fillable = [
        'created_by',
        'nama_loker',
        'posisi',
        'deskripsi',
        'jobdesk',
        'template_pakta_integritas',
        'is_active',
        'slug',
        'requirements',
        'tanggal_buka',
        'tanggal_tutup',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'requirements' => 'array',
        'tanggal_buka' => 'datetime',
        'tanggal_tutup'=> 'datetime',
    ];

    /**
     * Pelamar (applicants) yang melamar melalui lowongan ini.
     */
    public function recruitments()
    {
        return $this->hasMany(Recruitment::class, 'recruitment_post_id');
    }

    /**
     * Cek apakah lowongan masih terbuka (aktif dan dalam periode).
     */
    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        $now = now();
        if ($this->tanggal_buka && $now->lt($this->tanggal_buka)) {
            return false;
        }
        if ($this->tanggal_tutup && $now->gt($this->tanggal_tutup)) {
            return false;
        }
        return true;
    }

    /**
     * URL publik form pendaftaran.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('recruitment.form', $this->slug);
    }
}
