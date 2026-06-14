<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHistory extends Model
{
    protected $fillable = [
        'title',
        'year',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('year', 'desc');
    }

    /**
     * Get the timeline items for display
     */
    public static function getTimeline()
    {
        return self::orderBy('year', 'desc')
            ->get();
    }
}
