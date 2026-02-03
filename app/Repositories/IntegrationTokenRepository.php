<?php

namespace App\Repositories;

use App\Models\IntegrationToken;
use App\Repositories\Interfaces\IntegrationTokenRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class IntegrationTokenRepository implements IntegrationTokenRepositoryInterface
{
    public function __construct(
        protected IntegrationToken $model
    ) {}

    public function findById(int $id): ?IntegrationToken
    {
        return $this->model->find($id);
    }

    public function findByToken(string $token): ?IntegrationToken
    {
        return $this->model
            ->where('token', $token)
            ->first();
    }

    public function findBySlug(string $slug): ?IntegrationToken
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }

    public function findActiveByToken(string $token): ?IntegrationToken
    {
        return $this->model
            ->where('token', $token)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function create(array $data): IntegrationToken
    {
        return $this->model->create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'token' => $data['token'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'abilities' => $data['abilities'] ?? ['*'],
            'allowed_ips' => $data['allowed_ips'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'rate_limit_per_minute' => $data['rate_limit_per_minute'] ?? 60,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    public function update(IntegrationToken $integration, array $data): IntegrationToken
    {
        $integration->update($data);

        return $integration->fresh();
    }

    public function updateLastUsed(IntegrationToken $integration): void
    {
        $integration->update(['last_used_at' => now()]);
    }

    public function getAll(): Collection
    {
        return $this->model
            ->orderBy('name')
            ->get();
    }

    public function getByType(string $type): Collection
    {
        return $this->model
            ->where('type', $type)
            ->orderBy('name')
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('name')
            ->get();
    }

    public function delete(IntegrationToken $integration): bool
    {
        return $integration->delete();
    }
}
