<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    protected $fillable = [
        'company_profile_id',
        'section_key',
        'title',
        'content',
        'image',
        'link',
        'link_text',
        'sort_order',
        'extra_data',
        'is_active',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent company profile
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get features from extra_data
     */
    public function getFeaturesAttribute(): array
    {
        return $this->extra_data['features'] ?? [];
    }

    /**
     * Get stats from extra_data
     */
    public function getStatsAttribute(): array
    {
        return $this->extra_data['stats'] ?? [];
    }

    /**
     * Get testimonials from extra_data
     */
    public function getTestimonialsAttribute(): array
    {
        return $this->extra_data['testimonials'] ?? [];
    }
}
