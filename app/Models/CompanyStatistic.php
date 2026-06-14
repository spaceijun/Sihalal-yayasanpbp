<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyStatistic extends Model
{
    protected $fillable = [
        'title',
        'value',
        'suffix',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active stats
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
     * Get formatted value with suffix
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->value . $this->suffix;
    }

    /**
     * Get all active statistics
     */
    public static function getStats()
    {
        return self::active()
            ->ordered()
            ->get();
    }
}
