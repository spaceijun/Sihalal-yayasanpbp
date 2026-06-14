<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyProfile extends Model
{
    protected $fillable = [
        'page',
        'title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Get the sections for this page
     */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    /**
     * Get active sections only
     */
    public function activeSections(): HasMany
    {
        return $this->hasMany(PageSection::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Find profile by page name
     */
    public static function findByPage(string $page): ?self
    {
        return self::where('page', $page)->first();
    }
}
