<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User has many created POs
     */
    public function createdPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    /**
     * Relationship: User has many approved POs
     */
    public function approvedPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    /**
     * Relationship: User belongs to a department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relationship: User belongs to a branch / cabang
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Relationship: Departments where user is Default Manager L3
     */
    public function managedL3Departments()
    {
        return $this->hasMany(Department::class, 'manager_l3_id');
    }

    /**
     * Relationship: Departments where user is Default Manager L4
     */
    public function managedL4Departments()
    {
        return $this->hasMany(Department::class, 'manager_l4_id');
    }

    /**
     * Relationship: Departments where user is Default Manager L5
     */
    public function managedL5Departments()
    {
        return $this->hasMany(Department::class, 'manager_l5_id');
    }

    /**
     * Relationship: Branch Department where user is Manager L3
     */
    public function branchDepartmentL3()
    {
        return $this->hasMany(DepartmentBranch::class, 'manager_l3_id');
    }

    /**
     * Relationship: Branch Department where user is Manager L4
     */
    public function branchDepartmentL4()
    {
        return $this->hasMany(DepartmentBranch::class, 'manager_l4_id');
    }

    /**
     * Relationship: Branch Department where user is Manager L5
     */
    public function branchDepartmentL5()
    {
        return $this->hasMany(DepartmentBranch::class, 'manager_l5_id');
    }

    /**
     * Check if user can manage users (Admin only)
     */
    public function canManageUsers(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if user can approve POs (Manager, Regional Manager, General Manager, Admin, or has permission)
     */
    public function canApprovePO(): bool
    {
        return $this->hasRole(['Manager', 'Regional Manager', 'General Manager', 'Admin']) || $this->can('approve purchase-orders');
    }
}
