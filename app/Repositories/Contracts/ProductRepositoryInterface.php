<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredList(array $filters = [], int $perPage = 10);
    public function getActive();
    public function generateSku(): string;
}
