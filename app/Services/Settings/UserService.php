<?php

namespace App\Services\Settings;

use App\Repositories\Settings\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(array $filters)
    {
        return $this->userRepository->all($filters);
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->userRepository->create($data);
        });
    }

    public function findUser(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function updateUser(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->userRepository->update($id, $data);
        });
    }

    public function deleteUser(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->userRepository->delete($id);
        });
    }

    public function bulkDeleteUsers(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            return $this->userRepository->bulkDelete($ids);
        });
    }

    public function getUserStats()
    {
        return $this->userRepository->getStats();
    }
}
