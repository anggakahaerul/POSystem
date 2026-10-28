<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'min_amount',
        'max_amount',
        'approver_role',
        'level_label',
        'approver_for_staff',
        'approver_for_l3',
        'approver_for_l4',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Rule belongs to department (optional, null = global)
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific department or global
     */
    public function scopeForDepartment($query, ?int $departmentId)
    {
        if ($departmentId) {
            return $query->where(function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->orWhereNull('department_id');
            });
        }
        return $query->whereNull('department_id');
    }

    /**
     * Get formatted amount range
     */
    public function getAmountRangeLabelAttribute(): string
    {
        $min = 'Rp ' . number_format($this->min_amount, 0, ',', '.');
        if ($this->max_amount !== null && $this->max_amount > 0) {
            $max = 'Rp ' . number_format($this->max_amount, 0, ',', '.');
            return "{$min} - {$max}";
        }
        return "> {$min}";
    }

    /**
     * Get department name or Global
     */
    public function getDepartmentLabelAttribute(): string
    {
        return $this->department ? $this->department->name : 'Semua Departemen (Global)';
    }

    /**
     * Helper display label for approver level
     */
    public static function formatLevelLabel(?string $level): string
    {
        return match (strtoupper($level ?? '')) {
            'L3' => 'Manager Level 3',
            'L4' => 'Manager Level 4',
            'L5' => 'Manager Level 5',
            'DIRECTOR' => 'Direktur / Direksi',
            'NONE' => 'Tanpa Approval',
            default => $level ?: 'Manager Level 3',
        };
    }

    /**
     * Get target approver level needed based on requester level
     */
    public function getRequiredApproverLevel(string $requesterLevel = 'STAFF'): string
    {
        return match (strtoupper($requesterLevel)) {
            'L4' => $this->approver_for_l4 ?: 'L5',
            'L3' => $this->approver_for_l3 ?: 'L4',
            default => $this->approver_for_staff ?: 'L3',
        };
    }

    /**
     * Determine position / level of a PO creator
     */
    public static function determineRequesterLevel(User $user, ?Department $department = null, ?int $branchId = null): string
    {
        if ($department && $branchId) {
            $deptBranch = $department->departmentBranches()->where('branch_id', $branchId)->first();
            if ($deptBranch) {
                if ($deptBranch->manager_l4_id == $user->id) return 'L4';
                if ($deptBranch->manager_l3_id == $user->id) return 'L3';
            }
        }

        if ($department) {
            if ($department->manager_l4_id == $user->id) return 'L4';
            if ($department->manager_l3_id == $user->id) return 'L3';
        }

        if ($user->hasRole('Manager L4') || $user->hasRole('Regional Manager') || $user->hasRole('General Manager')) {
            return 'L4';
        }

        if ($user->hasRole('Manager L3') || $user->hasRole('Manager')) {
            return 'L3';
        }

        return 'STAFF';
    }

    /**
     * Find matching approval rule for a given department and amount
     */
    public static function findMatch(?int $departmentId, float $amount): ?self
    {
        // 1. Try department-specific rule first
        if ($departmentId) {
            $deptRule = self::active()
                ->where('department_id', $departmentId)
                ->where('min_amount', '<=', $amount)
                ->where(function ($q) use ($amount) {
                    $q->whereNull('max_amount')
                      ->orWhere('max_amount', '>=', $amount);
                })
                ->orderBy('min_amount', 'desc')
                ->first();

            if ($deptRule) {
                return $deptRule;
            }
        }

        // 2. Fallback to global rule
        $globalRule = self::active()
            ->whereNull('department_id')
            ->where('min_amount', '<=', $amount)
            ->where(function ($q) use ($amount) {
                $q->whereNull('max_amount')
                  ->orWhere('max_amount', '>=', $amount);
            })
            ->orderBy('min_amount', 'desc')
            ->first();

        if ($globalRule) {
            return $globalRule;
        }

        // 3. Fallback to the highest active rule if amount exceeds max defined ranges
        return self::active()
            ->where(function ($q) use ($departmentId) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId)->orWhereNull('department_id');
                } else {
                    $q->whereNull('department_id');
                }
            })
            ->orderByRaw('department_id IS NULL ASC')
            ->orderBy('min_amount', 'desc')
            ->first();
    }
}
