<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'photo',
        'name',
        'position',
        'company',
        'testimonial',
        'rating',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active testimonials
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
     * Get star rating as array
     */
    public function getStarsAttribute(): array
    {
        return array_fill(0, $this->rating, true);
    }

    /**
     * Get all active testimonials
     */
    public static function getTestimonials()
    {
        return self::active()
            ->ordered()
            ->get();
    }
}
