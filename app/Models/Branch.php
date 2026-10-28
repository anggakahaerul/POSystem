<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'npwp',
        'logo',
        'logo_base64',
        'footer_note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Active branches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relationship: Branch has many POs
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Relationship: Branch has many users / employees
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /**
     * Relationship: Branch has department manager settings
     */
    public function departmentBranches(): HasMany
    {
        return $this->hasMany(DepartmentBranch::class, 'branch_id');
    }

    /**
     * Get display name with code
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    /**
     * Convert month number to Roman numeral
     */
    public static function monthToRoman(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        return $romans[$month] ?? '';
    }
}
