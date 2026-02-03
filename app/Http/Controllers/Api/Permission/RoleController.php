<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\CreateRoleRequest;
use App\Http\Requests\Permission\UpdateRoleRequest;
use App\Http\Resources\Permission\RoleResource;
use App\Services\Permission\RoleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(
        private RoleService $roleService,
    ) {}

    public function index(): JsonResponse
    {
        $roles = $this->roleService->list();

        return $this->success(
            RoleResource::collection($roles),
            'Lista de roles'
        );
    }

    public function show(int $role): JsonResponse
    {
        $roleModel = $this->roleService->findById($role);

        if (!$roleModel) {
            return $this->notFound('Role não encontrada');
        }

        return $this->success(
            new RoleResource($roleModel),
            'Detalhes da role'
        );
    }

    public function store(CreateRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->toDTO());

        return $this->created(
            new RoleResource($role),
            'Role criada com sucesso'
        );
    }

    public function update(UpdateRoleRequest $request, int $role): JsonResponse
    {
        $roleModel = $this->roleService->findById($role);

        if (!$roleModel) {
            return $this->notFound('Role não encontrada');
        }

        if ($this->roleService->isProtectedRole($roleModel->name)) {
            return $this->forbidden('Não é permitido editar esta role');
        }

        $roleModel = $this->roleService->update($roleModel, $request->toDTO());

        return $this->success(
            new RoleResource($roleModel),
            'Role atualizada com sucesso'
        );
    }

    public function destroy(int $role): JsonResponse
    {
        $roleModel = $this->roleService->findById($role);

        if (!$roleModel) {
            return $this->notFound('Role não encontrada');
        }

        try {
            $this->roleService->delete($roleModel);
            return $this->success(null, 'Role excluída com sucesso');
        } catch (\InvalidArgumentException $e) {
            return $this->forbidden($e->getMessage());
        }
    }

    public function defaults(): JsonResponse
    {
        return $this->success(
            $this->roleService->getDefaultRoles(),
            'Roles padrão do sistema'
        );
    }
}
