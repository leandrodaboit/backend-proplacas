<?php

namespace App\Services\Integration;

use App\DTO\Integration\CreateIntegrationDTO;
use App\DTO\Integration\IntegrationResultDTO;
use App\Models\IntegrationToken;
use App\Repositories\Interfaces\IntegrationLogRepositoryInterface;
use App\Repositories\Interfaces\IntegrationTokenRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class IntegrationService
{
    public function __construct(
        private IntegrationTokenRepositoryInterface $tokenRepository,
        private IntegrationLogRepositoryInterface $logRepository,
    ) {}

    public function create(CreateIntegrationDTO $dto): IntegrationResultDTO
    {
        $plainToken = $this->generateToken();

        $integration = $this->tokenRepository->create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'type' => $dto->type,
            'description' => $dto->description,
            'abilities' => $dto->abilities,
            'allowed_ips' => $dto->allowedIps,
            'rate_limit_per_minute' => $dto->rateLimitPerMinute,
            'expires_at' => $dto->expiresAt,
            'is_active' => $dto->isActive,
            'token' => $plainToken,
        ]);

        Log::channel('daily')->info('Integration: created', [
            'id' => $integration->id,
            'name' => $integration->name,
            'type' => $integration->type,
        ]);

        return new IntegrationResultDTO($integration, $plainToken);
    }

    public function regenerateToken(IntegrationToken $integration): string
    {
        $plainToken = $this->generateToken();

        $this->tokenRepository->update($integration, ['token' => $plainToken]);

        Log::channel('daily')->info('Integration: token_regenerated', [
            'id' => $integration->id,
            'name' => $integration->name,
        ]);

        return $plainToken;
    }

    public function activate(IntegrationToken $integration): IntegrationToken
    {
        return $this->tokenRepository->update($integration, ['is_active' => true]);
    }

    public function deactivate(IntegrationToken $integration): IntegrationToken
    {
        return $this->tokenRepository->update($integration, ['is_active' => false]);
    }

    public function delete(IntegrationToken $integration): bool
    {
        Log::channel('daily')->info('Integration: deleted', [
            'id' => $integration->id,
            'name' => $integration->name,
        ]);

        return $this->tokenRepository->delete($integration);
    }

    public function findById(int $id): ?IntegrationToken
    {
        return $this->tokenRepository->findById($id);
    }

    public function findBySlug(string $slug): ?IntegrationToken
    {
        return $this->tokenRepository->findBySlug($slug);
    }

    public function findActiveByToken(string $token): ?IntegrationToken
    {
        return $this->tokenRepository->findActiveByToken($token);
    }

    public function list(?string $type = null): Collection
    {
        return $type
            ? $this->tokenRepository->getByType($type)
            : $this->tokenRepository->getAll();
    }

    public function listActive(): Collection
    {
        return $this->tokenRepository->getActive();
    }

    public function getStats(IntegrationToken $integration): array
    {
        return [
            'total_requests' => $this->logRepository->countByIntegration($integration),
            'requests_today' => $this->logRepository->countTodayByIntegration($integration),
            'average_response_time_ms' => $this->logRepository->getAverageResponseTime($integration),
            'error_rate' => $this->logRepository->getErrorRate($integration),
            'last_used_at' => $integration->last_used_at?->toIso8601String(),
        ];
    }

    public function logRequest(IntegrationToken $integration, array $data): void
    {
        $this->logRepository->create(array_merge($data, [
            'integration_token_id' => $integration->id,
            'created_at' => now(),
        ]));

        $this->tokenRepository->updateLastUsed($integration);
    }

    private function generateToken(): string
    {
        return hash('sha256', bin2hex(random_bytes(32)));
    }
}
