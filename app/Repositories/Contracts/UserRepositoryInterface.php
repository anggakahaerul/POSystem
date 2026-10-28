<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredList(array $filters = [], int $perPage = 10);
    public function createWithRoles(array $data, array $roles = []);
    public function updateWithRoles(int|string $id, array $data, ?array $roles = null);
}
