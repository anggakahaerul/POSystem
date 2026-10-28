<?php

namespace App\Repositories\Contracts;

interface PaymentTermRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredList(array $filters = [], int $perPage = 10);
    public function getActive();
}
