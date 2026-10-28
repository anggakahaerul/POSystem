<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->categoryRepository->getFilteredList($filters, $perPage);
    }

    public function getCategory(int|string $id)
    {
        return $this->categoryRepository->findOrFail($id);
    }

    public function createCategory(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = !empty($data['is_active']);
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int|string $id, array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = !empty($data['is_active']);
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int|string $id): array
    {
        $category = $this->categoryRepository->findOrFail($id);
        if ($category->products()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus kategori yang memiliki produk terkait.'
            ];
        }

        $this->categoryRepository->delete($id);
        return [
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ];
    }
}
