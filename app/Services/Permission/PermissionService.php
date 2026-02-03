<?php

namespace App\Services\Permission;

use App\Enums\PermissionEnum;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function list(): Collection
    {
        return $this->permissionRepository->getAll();
    }

    public function findById(int $id): ?Permission
    {
        return $this->permissionRepository->findById($id);
    }

    public function findByName(string $name): ?Permission
    {
        return $this->permissionRepository->findByName($name);
    }

    public function getGrouped(): array
    {
        return $this->permissionRepository->getGrouped();
    }

    public function getByModule(string $module): Collection
    {
        return $this->permissionRepository->getByModule($module);
    }

    public function getAvailableModules(): array
    {
        return PermissionEnum::modules();
    }

    public function getPermissionsFromEnum(): array
    {
        return PermissionEnum::grouped();
    }

    public function syncFromEnum(): void
    {
        $permissions = [];

        foreach (PermissionEnum::cases() as $permission) {
            $permissions[] = [
                'name' => $permission->value,
                'description' => $permission->label(),
                'module' => $permission->module(),
                'guard_name' => 'web',
            ];
        }

        $this->permissionRepository->createMany($permissions);

        Log::channel('daily')->info('Permission: synced_from_enum', [
            'count' => count($permissions),
        ]);
    }
}
