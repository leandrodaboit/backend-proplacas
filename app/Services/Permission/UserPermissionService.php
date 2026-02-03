<?php

namespace App\Services\Permission;

use App\DTO\Permission\AssignPermissionsDTO;
use App\DTO\Permission\AssignRoleDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPermissionService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function assignRoles(AssignRoleDTO $dto): User
    {
        $user = $this->userRepository->findById($dto->userId);

        if (!$user) {
            throw new \InvalidArgumentException('Usuário não encontrado');
        }

        return DB::transaction(function () use ($user, $dto) {
            $user->syncRoles($dto->roles);

            Log::channel('daily')->info('Permission: user_roles_assigned', [
                'user_id' => $user->id,
                'roles' => $dto->roles,
            ]);

            return $user->fresh(['roles', 'permissions']);
        });
    }

    public function assignPermissions(AssignPermissionsDTO $dto): User
    {
        $user = $this->userRepository->findById($dto->userId);

        if (!$user) {
            throw new \InvalidArgumentException('Usuário não encontrado');
        }

        return DB::transaction(function () use ($user, $dto) {
            $user->syncPermissions($dto->permissions);

            Log::channel('daily')->info('Permission: user_permissions_assigned', [
                'user_id' => $user->id,
                'permissions_count' => count($dto->permissions),
            ]);

            return $user->fresh(['roles', 'permissions']);
        });
    }

    public function revokeAllRoles(User $user): User
    {
        $user->syncRoles([]);

        Log::channel('daily')->info('Permission: user_roles_revoked', [
            'user_id' => $user->id,
        ]);

        return $user->fresh(['roles', 'permissions']);
    }

    public function revokeAllPermissions(User $user): User
    {
        $user->syncPermissions([]);

        Log::channel('daily')->info('Permission: user_permissions_revoked', [
            'user_id' => $user->id,
        ]);

        return $user->fresh(['roles', 'permissions']);
    }

    public function getUserPermissions(User $user): array
    {
        return [
            'roles' => $user->roles->pluck('name')->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'direct_permissions' => $user->getDirectPermissions()->pluck('name')->toArray(),
            'permissions_via_roles' => $user->getPermissionsViaRoles()->pluck('name')->toArray(),
        ];
    }

    public function hasPermission(User $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    public function hasAnyPermission(User $user, array $permissions): bool
    {
        return $user->hasAnyPermission($permissions);
    }

    public function hasAllPermissions(User $user, array $permissions): bool
    {
        return $user->hasAllPermissions($permissions);
    }

    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    public function hasAnyRole(User $user, array $roles): bool
    {
        return $user->hasAnyRole($roles);
    }
}
