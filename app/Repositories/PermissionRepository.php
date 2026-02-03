<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function __construct(
        private Permission $model,
    ) {}

    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function findById(int $id): ?Permission
    {
        return $this->model->find($id);
    }

    public function findByName(string $name, string $guardName = 'web'): ?Permission
    {
        return $this->model
            ->where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }

    public function create(array $data): Permission
    {
        return $this->model->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'module' => $data['module'] ?? null,
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function createMany(array $permissions): void
    {
        foreach ($permissions as $permission) {
            $this->model->firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name'] ?? 'web'],
                [
                    'description' => $permission['description'] ?? null,
                    'module' => $permission['module'] ?? null,
                ]
            );
        }
    }

    public function getByModule(string $module): Collection
    {
        return $this->model
            ->where('module', $module)
            ->orderBy('name')
            ->get();
    }

    public function getGrouped(): array
    {
        return $this->model
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module')
            ->toArray();
    }
}
