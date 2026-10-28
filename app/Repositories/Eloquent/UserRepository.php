<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getFilteredList(array $filters = [], int $perPage = 10)
    {
        $query = $this->model->with(['roles', 'department', 'branch']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createWithRoles(array $data, array $roles = [])
    {
        $user = $this->model->create($data);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
        return $user->load('roles');
    }

    public function updateWithRoles(int|string $id, array $data, ?array $roles = null)
    {
        $user = $this->findOrFail($id);
        $user->update($data);
        if ($roles !== null) {
            $user->syncRoles($roles);
        }
        return $user->fresh('roles');
    }
}
