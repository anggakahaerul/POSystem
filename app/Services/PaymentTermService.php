<?php

namespace App\Services;

use App\Repositories\Contracts\PaymentTermRepositoryInterface;

class PaymentTermService
{
    public function __construct(
        protected PaymentTermRepositoryInterface $paymentTermRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->paymentTermRepository->getFilteredList($filters, $perPage);
    }

    public function getPaymentTerm(int|string $id)
    {
        return $this->paymentTermRepository->findOrFail($id);
    }

    public function createPaymentTerm(array $data)
    {
        $data['days'] = ($data['payment_type'] ?? 'full') === 'full' ? (int) ($data['days'] ?? 0) : 0;
        $data['is_active'] = !empty($data['is_active']);
        return $this->paymentTermRepository->create($data);
    }

    public function updatePaymentTerm(int|string $id, array $data)
    {
        $data['days'] = ($data['payment_type'] ?? 'full') === 'full' ? (int) ($data['days'] ?? 0) : 0;
        $data['is_active'] = !empty($data['is_active']);
        return $this->paymentTermRepository->update($id, $data);
    }

    public function deletePaymentTerm(int|string $id): array
    {
        $term = $this->paymentTermRepository->findOrFail($id);
        if ($term->purchaseOrders()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus termin yang sudah digunakan pada Purchase Order.'
            ];
        }

        $this->paymentTermRepository->delete($id);
        return [
            'success' => true,
            'message' => 'Term pembayaran berhasil dihapus.'
        ];
    }
}
