<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(
        private Role $model,
    ) {}

    public function getAll(): Collection
    {
        return $this->model->with('permissions')->orderBy('name')->get();
    }

    public function findById(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    public function findByName(string $name, string $guardName = 'web'): ?Role
    {
        return $this->model
            ->where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }

    public function create(array $data): Role
    {
        return $this->model->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update(array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ], fn ($value) => $value !== null));

        return $role->fresh();
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function syncPermissions(Role $role, array $permissions): Role
    {
        $role->syncPermissions($permissions);

        return $role->fresh(['permissions']);
    }
}
