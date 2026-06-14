<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyTeam extends Model
{
    protected $fillable = [
        'photo',
        'name',
        'position',
        'description',
        'linkedin',
        'twitter',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active teams
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
     * Get the team members for display
     */
    public static function getTeam()
    {
        return self::active()
            ->ordered()
            ->get();
    }
}
