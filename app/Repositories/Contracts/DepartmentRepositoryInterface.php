<?php

namespace App\Repositories\Contracts;

interface DepartmentRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredList(array $filters = [], int $perPage = 10);
    public function getActive();
}
