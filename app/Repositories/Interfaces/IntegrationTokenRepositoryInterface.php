<?php

namespace App\Repositories\Interfaces;

use App\Models\IntegrationToken;
use Illuminate\Database\Eloquent\Collection;

interface IntegrationTokenRepositoryInterface
{
    public function findById(int $id): ?IntegrationToken;

    public function findByToken(string $token): ?IntegrationToken;

    public function findBySlug(string $slug): ?IntegrationToken;

    public function findActiveByToken(string $token): ?IntegrationToken;

    public function create(array $data): IntegrationToken;

    public function update(IntegrationToken $integration, array $data): IntegrationToken;

    public function updateLastUsed(IntegrationToken $integration): void;

    public function getAll(): Collection;

    public function getByType(string $type): Collection;

    public function getActive(): Collection;

    public function delete(IntegrationToken $integration): bool;
}
