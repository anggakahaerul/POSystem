<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentTerm;
use App\Repositories\Contracts\PaymentTermRepositoryInterface;

class PaymentTermRepository extends BaseRepository implements PaymentTermRepositoryInterface
{
    public function __construct(PaymentTerm $model)
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
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status'] == '1');
        }

        return $query->orderBy('days')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->orderBy('days')->get();
    }
}
