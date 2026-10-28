<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->productRepository->getFilteredList($filters, $perPage);
    }

    public function getProduct(int|string $id)
    {
        return $this->productRepository->findOrFail($id);
    }

    public function createProduct(array $data)
    {
        if (isset($data['price']) && $data['price'] !== '') {
            $data['price'] = (float) str_replace(['.', ','], '', (string) $data['price']);
        } else {
            $data['price'] = 0;
        }
        $data['stock'] = isset($data['stock']) ? (int) $data['stock'] : 0;
        $data['min_stock'] = isset($data['min_stock']) ? (int) $data['min_stock'] : 0;
        $data['is_active'] = !empty($data['is_active']);

        return $this->productRepository->create($data);
    }

    public function updateProduct(int|string $id, array $data)
    {
        if (isset($data['price']) && $data['price'] !== '') {
            $data['price'] = (float) str_replace(['.', ','], '', (string) $data['price']);
        } elseif (array_key_exists('price', $data)) {
            $data['price'] = 0;
        }
        $data['stock'] = isset($data['stock']) ? (int) $data['stock'] : 0;
        $data['min_stock'] = isset($data['min_stock']) ? (int) $data['min_stock'] : 0;
        $data['is_active'] = !empty($data['is_active']);

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int|string $id): array
    {
        $product = $this->productRepository->findOrFail($id);
        if ($product->purchaseOrderDetails()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus produk yang sudah tercatat dalam Purchase Order.'
            ];
        }

        $this->productRepository->delete($id);
        return [
            'success' => true,
            'message' => 'Produk berhasil dihapus.'
        ];
    }

    public function generateSku(): string
    {
        return $this->productRepository->generateSku();
    }
}
