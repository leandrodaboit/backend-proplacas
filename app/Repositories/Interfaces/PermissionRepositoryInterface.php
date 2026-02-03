<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

interface PermissionRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Permission;

    public function findByName(string $name, string $guardName = 'web'): ?Permission;

    public function create(array $data): Permission;

    public function createMany(array $permissions): void;

    public function getByModule(string $module): Collection;

    public function getGrouped(): array;
}
