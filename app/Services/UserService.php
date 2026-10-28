<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedList(array $filters = [], int $perPage = 10)
    {
        return $this->userRepository->getFilteredList($filters, $perPage);
    }

    public function getUser(int|string $id)
    {
        return $this->userRepository->findOrFail($id);
    }

    public function createUser(array $data)
    {
        $role = $data['role'] ?? null;
        unset($data['role']);

        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        if ($role) {
            $user->assignRole($role);
        }

        return $user->load('roles');
    }

    public function updateUser(int|string $id, array $data)
    {
        $role = $data['role'] ?? null;
        unset($data['role']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->update($id, $data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return $user->load('roles');
    }

    public function deleteUser(int|string $id, int|string $currentUserId): array
    {
        if ($id == $currentUserId) {
            return [
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.'
            ];
        }

        $user = $this->userRepository->findOrFail($id);
        if ($user->createdPurchaseOrders()->count() > 0) {
            return [
                'success' => false,
                'message' => 'User tidak dapat dihapus karena memiliki riwayat pembuatan Purchase Order.'
            ];
        }

        $this->userRepository->delete($id);
        return [
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ];
    }
}
