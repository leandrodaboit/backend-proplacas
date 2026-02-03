<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\AssignPermissionsRequest;
use App\Http\Requests\Permission\AssignRolesRequest;
use App\Http\Resources\Permission\UserPermissionsResource;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Permission\UserPermissionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserPermissionService $userPermissionService,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function show(int $user): JsonResponse
    {
        $userModel = $this->userRepository->findById($user);

        if (!$userModel) {
            return $this->notFound('Usuário não encontrado');
        }

        return $this->success(
            new UserPermissionsResource($userModel),
            'Permissões do usuário'
        );
    }

    public function me(Request $request): JsonResponse
    {
        $permissions = $this->userPermissionService->getUserPermissions($request->user());

        return $this->success($permissions, 'Suas permissões');
    }

    public function assignRoles(AssignRolesRequest $request, int $user): JsonResponse
    {
        try {
            $userModel = $this->userPermissionService->assignRoles($request->toDTO());

            return $this->success(
                new UserPermissionsResource($userModel),
                'Roles atribuídas com sucesso'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function assignPermissions(AssignPermissionsRequest $request, int $user): JsonResponse
    {
        try {
            $userModel = $this->userPermissionService->assignPermissions($request->toDTO());

            return $this->success(
                new UserPermissionsResource($userModel),
                'Permissões atribuídas com sucesso'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function revokeRoles(int $user): JsonResponse
    {
        $userModel = $this->userRepository->findById($user);

        if (!$userModel) {
            return $this->notFound('Usuário não encontrado');
        }

        $userModel = $this->userPermissionService->revokeAllRoles($userModel);

        return $this->success(
            new UserPermissionsResource($userModel),
            'Todas as roles foram revogadas'
        );
    }

    public function revokePermissions(int $user): JsonResponse
    {
        $userModel = $this->userRepository->findById($user);

        if (!$userModel) {
            return $this->notFound('Usuário não encontrado');
        }

        $userModel = $this->userPermissionService->revokeAllPermissions($userModel);

        return $this->success(
            new UserPermissionsResource($userModel),
            'Todas as permissões diretas foram revogadas'
        );
    }

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'permission' => ['required_without:role', 'string'],
            'role' => ['required_without:permission', 'string'],
        ]);

        $user = $request->user();

        if ($request->has('permission')) {
            $hasPermission = $this->userPermissionService->hasPermission($user, $request->permission);
            return $this->success([
                'permission' => $request->permission,
                'has_permission' => $hasPermission,
            ], $hasPermission ? 'Usuário possui a permissão' : 'Usuário não possui a permissão');
        }

        $hasRole = $this->userPermissionService->hasRole($user, $request->role);
        return $this->success([
            'role' => $request->role,
            'has_role' => $hasRole,
        ], $hasRole ? 'Usuário possui a role' : 'Usuário não possui a role');
    }
}
