<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\AuthenticationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use App\Http\Resources\Auth\TokenCollection;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->toDTO());

        return $this->created(
            new AuthenticatedResource(['user' => $result->user, 'token' => $result->token]),
            'Usuário cadastrado com sucesso'
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->toDTO());

            return $this->success(
                new AuthenticatedResource(['user' => $result->user, 'token' => $result->token]),
                'Login realizado com sucesso'
            );
        } catch (AuthenticationException $e) {
            return $this->unauthorized($e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logout realizado com sucesso');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return $this->success(null, 'Logout de todos os dispositivos realizado');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'Dados do usuário autenticado'
        );
    }

    public function tokens(Request $request): JsonResponse
    {
        $tokens = $this->authService->listTokens($request->user());

        return $this->success(
            new TokenCollection($tokens),
            'Lista de tokens ativos'
        );
    }

    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        if (!$this->authService->revokeToken($request->user(), $tokenId)) {
            return $this->notFound('Token não encontrado');
        }

        return $this->success(null, 'Token revogado com sucesso');
    }
}
