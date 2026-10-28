<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredList(array $filters = [], int $perPage = 10);
}
