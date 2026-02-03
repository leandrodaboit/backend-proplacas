<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function updateLastLogin(User $user, ?string $ip = null): void;

    public function findActiveByEmail(string $email): ?User;

    public function getAll(): Collection;

    public function getByStatus(string $status): Collection;

    public function getByTipo(string $tipo): Collection;
}
