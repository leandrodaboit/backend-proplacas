<?php

namespace App\Services\Permission;

use App\DTO\Permission\CreateRoleDTO;
use App\DTO\Permission\UpdateRoleDTO;
use App\Enums\RoleEnum;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
    ) {}

    public function list(): Collection
    {
        return $this->roleRepository->getAll();
    }

    public function findById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->roleRepository->findByName($name);
    }

    public function create(CreateRoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto) {
            $role = $this->roleRepository->create([
                'name' => $dto->name,
                'description' => $dto->description,
                'guard_name' => $dto->guardName,
            ]);

            if (!empty($dto->permissions)) {
                $this->roleRepository->syncPermissions($role, $dto->permissions);
            }

            Log::channel('daily')->info('Permission: role_created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            return $role->fresh(['permissions']);
        });
    }

    public function update(Role $role, UpdateRoleDTO $dto): Role
    {
        return DB::transaction(function () use ($role, $dto) {
            if ($dto->name || $dto->description) {
                $this->roleRepository->update($role, [
                    'name' => $dto->name,
                    'description' => $dto->description,
                ]);
            }

            if ($dto->permissions !== null) {
                $this->roleRepository->syncPermissions($role, $dto->permissions);
            }

            Log::channel('daily')->info('Permission: role_updated', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            return $role->fresh(['permissions']);
        });
    }

    public function delete(Role $role): bool
    {
        if ($this->isProtectedRole($role->name)) {
            throw new \InvalidArgumentException('Não é possível excluir roles do sistema');
        }

        Log::channel('daily')->info('Permission: role_deleted', [
            'role_id' => $role->id,
            'role_name' => $role->name,
        ]);

        return $this->roleRepository->delete($role);
    }

    public function syncPermissions(Role $role, array $permissions): Role
    {
        Log::channel('daily')->info('Permission: role_permissions_synced', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions_count' => count($permissions),
        ]);

        return $this->roleRepository->syncPermissions($role, $permissions);
    }

    public function getDefaultRoles(): array
    {
        return RoleEnum::toArray();
    }

    public function isProtectedRole(string $roleName): bool
    {
        return in_array($roleName, [
            RoleEnum::SUPER_ADMIN->value,
        ]);
    }
}
