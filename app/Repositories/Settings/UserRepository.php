<?php

namespace App\Repositories\Settings;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator|Collection
    {
        $query = User::with('roles');

        if (isset($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['q'] . '%')
                  ->orWhereHas('roles', function ($q) use ($filters) {
                      $q->where('name', 'like', '%' . $filters['q'] . '%');
                  });
            });
        }

        if (isset($filters['role_id'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('id', $filters['role_id']);
            });
        }

        if (isset($filters['status'])) {
            // Assuming 'status' maps to some field or logic in User model
            // For now, let's assume a simple 'is_active' boolean or similar
            // You might need to adjust this based on your actual User model status logic
            $query->where('status', $filters['status']); 
        }

        $sort = $filters['sort'] ?? 'id';
        $sortDir = $filters['sortDir'] ?? 'desc';
        $query->orderBy($sort, $sortDir);

        if (isset($filters['limit'])) {
            return $query->paginate($filters['limit']);
        }

        return $query->get();
    }

    public function findById(int $id): ?User
    {
        return User::with('roles', 'vessels', 'vessel')->find($id);
    }

    public function create(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->password = Hash::make($data['password']);
        $user->vessel_id = $data['vessel_id'];
        $user->save();

        if (isset($data['role'])) {
            $role = Role::findById($data['role']);
            $user->assignRole($role);
        }

        if (!empty($data['image'])) {
            $user->image = $this->uploadImage($data['image']);
            $user->save();
        }

        if (isset($data['vessel_list'])) {
            $user->vessels()->sync(explode(',',$data['vessel_list']));
        }

        return $user;
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->vessel_id = $data['vessel_id'];

        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (isset($data['role'])) {
            $role = Role::findById($data['role']);
            $user->syncRoles($role);
        }

        if (!empty($data['image'])) {
            // Delete old image if exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $user->image = $this->uploadImage($data['image']);
        }

        $user->save();

        if (isset($data['vessel_list'])) {
            $user->vessels()->sync(explode(',',$data['vessel_list']));
        }

        return $user;
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }
        // Optionally delete user image
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        return $user->delete();
    }

    public function bulkDelete(array $ids): bool
    {
        // Prevent deleting user with ID 1 (Admin)
        $ids = array_diff($ids, [1]);
        if (empty($ids)) {
            return false;
        }

        $users = User::whereIn('id', $ids)->get();
        foreach ($users as $user) {
            $this->delete($user->id);
        }
        return true;
    }

    public function getStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(), // Assuming a 'status' column
            'inactive' => User::where('status', 'inactive')->count(), // Assuming a 'status' column
            'admin' => User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })->count(),
        ];
    }

    protected function uploadImage($imageFile)
    {
        if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
            return $imageFile->store('users', 'public');
        }
        // If it's already a string (path), return it as is
        return $imageFile;
    }
}
