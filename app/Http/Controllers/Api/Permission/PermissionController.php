<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use App\Http\Resources\Permission\PermissionResource;
use App\Services\Permission\PermissionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PermissionService $permissionService,
    ) {}

    public function index(): JsonResponse
    {
        $permissions = $this->permissionService->list();

        return $this->success(
            PermissionResource::collection($permissions),
            'Lista de permissões'
        );
    }

    public function grouped(): JsonResponse
    {
        $permissions = $this->permissionService->getGrouped();

        return $this->success($permissions, 'Permissões agrupadas por módulo');
    }

    public function modules(): JsonResponse
    {
        $modules = $this->permissionService->getAvailableModules();

        return $this->success($modules, 'Módulos disponíveis');
    }

    public function byModule(string $module): JsonResponse
    {
        $permissions = $this->permissionService->getByModule($module);

        return $this->success(
            PermissionResource::collection($permissions),
            "Permissões do módulo {$module}"
        );
    }

    public function available(): JsonResponse
    {
        $permissions = $this->permissionService->getPermissionsFromEnum();

        return $this->success($permissions, 'Permissões disponíveis no sistema');
    }
}
