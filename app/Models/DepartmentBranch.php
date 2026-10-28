<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentBranch extends Model
{
    use HasFactory;

    protected $table = 'department_branches';

    protected $fillable = [
        'department_id',
        'branch_id',
        'manager_l3_id',
        'manager_l4_id',
        'manager_l5_id',
    ];

    /**
     * Departemen induk
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Cabang penugasan
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Manager Level 3 (L3)
     */
    public function managerL3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_l3_id');
    }

    /**
     * Manager Level 4 (L4)
     */
    public function managerL4(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_l4_id');
    }

    /**
     * Manager Level 5 (L5)
     */
    public function managerL5(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_l5_id');
    }
}
