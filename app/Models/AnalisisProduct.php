<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnalisisProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'analisis_products';

    protected $fillable = [
        'product_name',
        'kemasan',
        'bahan',
        'proses',
        'catatan_halal',
        'status_halal',
        'sertifikasi',
        'google_search_url',
        'image_path',
        'raw_analysis',
        'user_id',
        'data_lapangan_id',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'bahan' => 'array',
        'proses' => 'array',
        'raw_analysis' => 'array',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Status halal options
     */
    public const STATUS_HALAL = [
        'AMAN' => 'AMAN',
        'PERLU_VERIFIKASI' => 'PERLU_VERIFIKASI',
        'BERISIKO' => 'BERISIKO',
        'TIDAK_HALAL' => 'TIDAK_HALAL',
    ];

    /**
     * User who created this analisis
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Data lapangan linked to this analisis
     */
    public function dataLapangan(): BelongsTo
    {
        return $this->belongsTo(DataLapangan::class, 'data_lapangan_id');
    }

    /**
     * Admin who approved this analisis
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: approved products only
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: pending approval
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Find by product name (case insensitive)
     */
    public static function findByName(string $name): ?self
    {
        return static::whereRaw('LOWER(product_name) = ?', [strtolower($name)])->first();
    }

    /**
     * Check if product exists in database
     */
    public static function exists(string $name): bool
    {
        return static::whereRaw('LOWER(product_name) = ?', [strtolower($name)])->exists();
    }

    /**
     * Approve this analisis
     */
    public function approve(User $approver): bool
    {
        $this->is_approved = true;
        $this->approved_at = now();
        $this->approved_by = $approver->id;

        return $this->save();
    }
}
