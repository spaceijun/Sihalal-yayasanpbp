<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ArticleCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the articles for this category
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category', 'name');
    }

    /**
     * Get all categories with article count
     */
    public static function getAllWithCount()
    {
        return self::withCount('articles')
            ->orderBy('name')
            ->get();
    }
}
