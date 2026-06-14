<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBenefit extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active benefits
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get all active benefits
     */
    public static function getBenefits()
    {
        return self::active()
            ->ordered()
            ->get();
    }
}
