<?php

namespace App\Repositories\Contracts;

interface VendorRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredList(array $filters = [], int $perPage = 10);
    public function generateCode(): string;
}
