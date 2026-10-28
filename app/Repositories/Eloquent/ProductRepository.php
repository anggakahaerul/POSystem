<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getFilteredList(array $filters = [], int $perPage = 10)
    {
        $query = $this->model->with('category');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['asset'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('asset', $filters['asset']);
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
        return $this->model->where('is_active', true)->orderBy('name')->get();
    }

    public function generateSku(): string
    {
        return Product::generateSku();
    }
}
