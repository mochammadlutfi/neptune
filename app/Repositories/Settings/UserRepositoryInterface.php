<?php

namespace App\Repositories\Settings;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\User;

interface UserRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator|Collection;
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
    public function getStats(): array;
    public function bulkDelete(array $ids): bool;
}
