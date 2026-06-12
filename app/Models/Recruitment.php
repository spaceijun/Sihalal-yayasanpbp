<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recruitment
 *
 * @property $id
 * @property $recruitment_post_id
 * @property $koordinator_id
 * @property $recruit_type
 * @property $type_entry
 * @property $nama_lengkap
 * @property $nik
 * @property $jenis_kelamin
 * @property $telephone
 * @property $alamat_lengkap
 * @property $pengalaman
 * @property $rekomendasi
 * @property $pendidikan_terakhir
 * @property $foto_diri
 * @property $foto_ktp
 * @property $foto_ijasah
 * @property $pakta_integritas
 * @property $answers
 * @property $status
 * @property $alasan_penolakan
 * @property $created_at
 * @property $updated_at
 * @property \App\Models\RecruitmentPost $recruitmentPost
 * @property \App\Models\Superadmin\Koordinator $koordinator
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Recruitment extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['recruitment_post_id', 'koordinator_id', 'recruit_type', 'type_entry', 'nama_lengkap', 'nik', 'jenis_kelamin', 'telephone', 'alamat_lengkap', 'pengalaman', 'rekomendasi', 'pendidikan_terakhir', 'foto_diri', 'foto_ktp', 'foto_ijasah', 'pakta_integritas', 'answers', 'status', 'alasan_penolakan'];

    protected $casts = [
        'answers' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recruitmentPost()
    {
        return $this->belongsTo(\App\Models\RecruitmentPost::class, 'recruitment_post_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function koordinator()
    {
        return $this->belongsTo(\App\Models\Superadmin\Koordinator::class, 'koordinator_id', 'id');
    }
}
