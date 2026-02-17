<?php

namespace App\Services\Auth;

use App\DTO\Auth\AuthResultDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Exceptions\AuthenticationException;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenService $tokenService,
    ) {}

    public function register(RegisterDTO $dto): AuthResultDTO
    {
        return DB::transaction(function () use ($dto) {
            // 1. Criar o usuário
            $user = $this->userRepository->create([
                'name' => $dto->name,
                'sobrenome' => $dto->sobrenome,
                'email' => $dto->email,
                'telefone' => $dto->telefone,
                'password' => $dto->password,
                'tipo' => $dto->tipo,
            ]);

            // 2. Buscar ou criar o plano padrão (Free)
            $plan = Plan::firstOrCreate(
                ['code' => 'free'],
                [
                    'name' => 'Free',
                    'price' => 0.00,
                    'limits' => json_encode(['users' => 1, 'storage' => '1GB']),
                    'is_active' => true
                ]
            );

            // 3. Criar a empresa
            $company = Company::create([
                'name' => $dto->empresa,
                'plan_id' => $plan->id,
                'status' => 'active'
            ]);

            // 4. Vincular usuário à empresa como owner
            $company->users()->attach($user->id, [
                'is_owner' => true,
                'is_active' => true
            ]);

            // 5. Gerar token e logar
            $token = $this->tokenService->createToken($user, $dto->deviceName, $dto->ip);
            $this->userRepository->updateLastLogin($user, $dto->ip);

            Log::channel('daily')->info('Auth: register', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'email' => $user->email,
                'ip' => $dto->ip,
            ]);

            return new AuthResultDTO($user, $token);
        });
    }

    public function login(LoginDTO $dto): AuthResultDTO
    {
        $user = $this->userRepository->findByEmail($dto->email);

        $this->validateCredentials($user, $dto->password, $dto->email, $dto->ip);
        $this->validateUserStatus($user);

        $token = $this->tokenService->createToken($user, $dto->deviceName, $dto->ip);

        $this->userRepository->updateLastLogin($user, $dto->ip);

        Log::channel('daily')->info('Auth: login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $dto->ip,
        ]);

        return new AuthResultDTO($user, $token);
    }

    public function logout(User $user): void
    {
        $this->tokenService->revokeCurrentToken($user);

        Log::channel('daily')->info('Auth: logout', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function logoutAll(User $user): void
    {
        $this->tokenService->revokeAllTokens($user);

        Log::channel('daily')->info('Auth: logout_all', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function listTokens(User $user): Collection
    {
        return $this->tokenService->listTokens($user);
    }

    public function revokeToken(User $user, int $tokenId): bool
    {
        $revoked = $this->tokenService->revokeToken($user, $tokenId);

        if ($revoked) {
            Log::channel('daily')->info('Auth: token_revoked', [
                'user_id' => $user->id,
                'token_id' => $tokenId,
            ]);
        }

        return $revoked;
    }

    private function validateCredentials(?User $user, string $password, string $email, string $ip): void
    {
        if (!$user || !Hash::check($password, $user->password)) {
            Log::channel('daily')->warning('Auth: login_failed', [
                'email' => $email,
                'ip' => $ip,
            ]);

            throw new AuthenticationException('Credenciais inválidas');
        }
    }

    private function validateUserStatus(User $user): void
    {
        if (!$user->ativo) {
            throw new AuthenticationException('Usuário desativado');
        }

        if ($user->status !== 'active') {
            throw new AuthenticationException("Usuário com status: {$user->status}");
        }
    }
}
