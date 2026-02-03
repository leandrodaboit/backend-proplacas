<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }

    public function findActiveByEmail(string $email): ?User
    {
        return $this->model
            ->where('email', $email)
            ->where('ativo', true)
            ->where('status', 'active')
            ->first();
    }

    public function create(array $data): User
    {
        return $this->model->create([
            'name' => $data['name'],
            'sobrenome' => $data['sobrenome'] ?? null,
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'password' => $data['password'],
            'tipo' => $data['tipo'] ?? 'cliente',
            'status' => 'active',
            'ativo' => true,
        ]);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function updateLastLogin(User $user, ?string $ip = null): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function getAll(): Collection
    {
        return $this->model
            ->orderBy('name')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->orderBy('name')
            ->get();
    }

    public function getByTipo(string $tipo): Collection
    {
        return $this->model
            ->where('tipo', $tipo)
            ->orderBy('name')
            ->get();
    }
}
