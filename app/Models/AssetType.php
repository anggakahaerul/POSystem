<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'badge_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Hanya tipe asset yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relasi ke Category berdasarkan kode asset
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'asset', 'code');
    }

    /**
     * Hitung total kategori yang menggunakan tipe asset ini
     */
    public function getCategoriesCountAttribute(): int
    {
        return $this->categories()->count();
    }
}
