<?php

namespace App\Services;

use App\Repositories\Contracts\BranchRepositoryInterface;

class BranchService
{
    public function __construct(
        protected BranchRepositoryInterface $branchRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->branchRepository->getFilteredList($filters, $perPage);
    }

    public function getBranch(int|string $id)
    {
        return $this->branchRepository->findOrFail($id);
    }

    public function createBranch(array $data)
    {
        $data['is_active'] = !empty($data['is_active']);
        $data['code'] = strtoupper($data['code']);

        if (!empty($data['logo_base64']) && preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $data['logo_base64'])) {
            $data['logo'] = 'logo_' . time() . '.png';
        }

        return $this->branchRepository->create($data);
    }

    public function updateBranch(int|string $id, array $data)
    {
        $data['is_active'] = !empty($data['is_active']);
        $data['code'] = strtoupper($data['code']);

        if (!empty($data['logo_base64']) && preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $data['logo_base64'])) {
            $data['logo'] = 'logo_' . time() . '.png';
        }

        return $this->branchRepository->update($id, $data);
    }

    public function deleteBranch(int|string $id): array
    {
        $branch = $this->branchRepository->findOrFail($id);
        if ($branch->purchaseOrders()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Cabang masih memiliki riwayat PO terkait, tidak dapat dihapus.'
            ];
        }

        $this->branchRepository->delete($id);
        return [
            'success' => true,
            'message' => 'Cabang berhasil dihapus.'
        ];
    }
}
