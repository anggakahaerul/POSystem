<?php

namespace App\Repositories\Eloquent;

use App\Models\Department;
use App\Repositories\Contracts\DepartmentRepositoryInterface;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

    public function getFilteredList(array $filters = [], int $perPage = 10)
    {
        $query = $this->model->with([
            'managerL3',
            'managerL4',
            'managerL5',
            'departmentBranches.branch',
            'departmentBranches.managerL3',
            'departmentBranches.managerL4',
            'departmentBranches.managerL5',
        ])->withCount(['users', 'departmentBranches']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status'] == '1');
        }

        return $query->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getActive()
    {
        return $this->model->active()->orderBy('name')->get();
    }
}
