<?php

namespace App\Services;

use App\Repositories\Contracts\VendorRepositoryInterface;

class VendorService
{
    public function __construct(
        protected VendorRepositoryInterface $vendorRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->vendorRepository->getFilteredList($filters, $perPage);
    }

    public function getVendor(int|string $id)
    {
        return $this->vendorRepository->findOrFail($id);
    }

    public function createVendor(array $data)
    {
        $data['is_active'] = !empty($data['is_active']);
        return $this->vendorRepository->create($data);
    }

    public function updateVendor(int|string $id, array $data)
    {
        $data['is_active'] = !empty($data['is_active']);
        return $this->vendorRepository->update($id, $data);
    }

    public function deleteVendor(int|string $id): array
    {
        $vendor = $this->vendorRepository->findOrFail($id);
        if ($vendor->purchaseOrders()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus vendor yang memiliki riwayat Purchase Order.'
            ];
        }

        $this->vendorRepository->delete($id);
        return [
            'success' => true,
            'message' => 'Vendor berhasil dihapus.'
        ];
    }

    public function generateCode(): string
    {
        return $this->vendorRepository->generateCode();
    }
}
