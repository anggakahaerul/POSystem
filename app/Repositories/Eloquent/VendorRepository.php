<?php

namespace App\Repositories\Eloquent;

use App\Models\Vendor;
use App\Repositories\Contracts\VendorRepositoryInterface;

class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }

    public function getFilteredList(array $filters = [], int $perPage = 10)
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status'] == '1');
        }

        return $query->with('paymentTerm')
            ->withCount('purchaseOrders')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function generateCode(): string
    {
        return Vendor::generateCode();
    }
}
