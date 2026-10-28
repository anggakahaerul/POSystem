<?php

namespace App\Services;

use App\Repositories\Contracts\DepartmentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->departmentRepository->getFilteredList($filters, $perPage);
    }

    public function getActiveList()
    {
        return $this->departmentRepository->getActive();
    }

    public function getDepartment(int|string $id)
    {
        return $this->departmentRepository->findOrFail($id);
    }

    public function createDepartment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $branchesData = $data['branches'] ?? null;
            unset($data['branches']);

            $data['code'] = strtoupper($data['code']);
            $data['is_active'] = !empty($data['is_active']);
            $data['manager_l3_id'] = !empty($data['manager_l3_id']) ? $data['manager_l3_id'] : null;
            $data['manager_l4_id'] = !empty($data['manager_l4_id']) ? $data['manager_l4_id'] : null;
            $data['manager_l5_id'] = !empty($data['manager_l5_id']) ? $data['manager_l5_id'] : null;

            $department = $this->departmentRepository->create($data);

            if (!empty($branchesData) && is_array($branchesData)) {
                $this->syncDepartmentBranches($department, $branchesData);
            }

            return $department->load([
                'managerL3', 'managerL4', 'managerL5',
                'departmentBranches.branch',
                'departmentBranches.managerL3',
                'departmentBranches.managerL4',
                'departmentBranches.managerL5',
            ]);
        });
    }

    public function updateDepartment(int|string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $branchesData = $data['branches'] ?? null;
            unset($data['branches']);

            $data['code'] = strtoupper($data['code']);
            $data['is_active'] = !empty($data['is_active']);
            $data['manager_l3_id'] = !empty($data['manager_l3_id']) ? $data['manager_l3_id'] : null;
            $data['manager_l4_id'] = !empty($data['manager_l4_id']) ? $data['manager_l4_id'] : null;
            $data['manager_l5_id'] = !empty($data['manager_l5_id']) ? $data['manager_l5_id'] : null;

            $department = $this->departmentRepository->update($id, $data);

            if ($branchesData !== null && is_array($branchesData)) {
                $this->syncDepartmentBranches($department, $branchesData);
            }

            return $department->load([
                'managerL3', 'managerL4', 'managerL5',
                'departmentBranches.branch',
                'departmentBranches.managerL3',
                'departmentBranches.managerL4',
                'departmentBranches.managerL5',
            ]);
        });
    }

    /**
     * Sync department branch managers
     */
    protected function syncDepartmentBranches($department, array $branchesData): void
    {
        foreach ($branchesData as $bData) {
            $branchId = $bData['branch_id'] ?? null;
            if (!$branchId) {
                continue;
            }

            $l3 = !empty($bData['manager_l3_id']) ? $bData['manager_l3_id'] : null;
            $l4 = !empty($bData['manager_l4_id']) ? $bData['manager_l4_id'] : null;
            $l5 = !empty($bData['manager_l5_id']) ? $bData['manager_l5_id'] : null;

            // If at least one manager is set for this branch, save/update
            if ($l3 || $l4 || $l5) {
                $department->departmentBranches()->updateOrCreate(
                    ['branch_id' => $branchId],
                    [
                        'manager_l3_id' => $l3,
                        'manager_l4_id' => $l4,
                        'manager_l5_id' => $l5,
                    ]
                );
            } else {
                // Remove branch specific setting so it falls back to default
                $department->departmentBranches()->where('branch_id', $branchId)->delete();
            }
        }
    }

    public function deleteDepartment(int|string $id): array
    {
        $department = $this->departmentRepository->findOrFail($id);
        if ($department->users()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus departemen yang masih memiliki karyawan/user terdaftar.'
            ];
        }

        $this->departmentRepository->delete($id);
        return [
            'success' => true,
            'message' => 'Departemen berhasil dihapus.'
        ];
    }
}
