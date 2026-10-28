<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'manager_l3_id',
        'manager_l4_id',
        'manager_l5_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Default / Head Office Manager Level 3 (L3)
     */
    public function managerL3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_l3_id');
    }

    /**
     * Default / Head Office Manager Level 4 (L4)
     */
    public function managerL4(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_l4_id');
    }

    /**
     * Default / Head Office Manager Level 5 (L5)
     */
    public function managerL5(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_l5_id');
    }

    /**
     * Pengaturan Manager Level 3, 4, 5 per Cabang
     */
    public function departmentBranches(): HasMany
    {
        return $this->hasMany(DepartmentBranch::class);
    }

    /**
     * Anggota / Karyawan dalam departemen ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Aturan approval khusus departemen ini
     */
    public function approvalRules(): HasMany
    {
        return $this->hasMany(ApprovalRule::class);
    }

    /**
     * Helper untuk mendapatkan Manager pada Cabang tertentu (dengan fallback ke default HO)
     */
    public function getManagerForBranch(?int $branchId, string $level = 'l3'): ?User
    {
        $levelField = match (strtolower($level)) {
            'l3', 'level3', 'level_3' => 'manager_l3_id',
            'l4', 'level4', 'level_4' => 'manager_l4_id',
            'l5', 'level5', 'level_5' => 'manager_l5_id',
            default => 'manager_l3_id',
        };

        if ($branchId) {
            $deptBranch = $this->departmentBranches()->where('branch_id', $branchId)->first();
            if ($deptBranch && $deptBranch->{$levelField}) {
                $relation = match ($levelField) {
                    'manager_l3_id' => 'managerL3',
                    'manager_l4_id' => 'managerL4',
                    'manager_l5_id' => 'managerL5',
                };
                return $deptBranch->{$relation};
            }
        }

        return null;
    }

    /**
     * Scope for active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
