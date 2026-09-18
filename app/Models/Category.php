<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    const ASSET_HRGA = 'HRGA';
    const ASSET_MIS = 'MIS';
    const ASSETS = [
        self::ASSET_HRGA => 'HRGA',
        self::ASSET_MIS => 'MIS',
    ];

    protected $fillable = [
        'name',
        'asset',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot method - auto generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relationship: Category has many products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope: Active categories only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get products count attribute
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
