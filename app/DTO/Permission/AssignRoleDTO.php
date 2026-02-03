<?php

namespace App\DTO\Permission;

use Spatie\LaravelData\Data;

class AssignRoleDTO extends Data
{
    public function __construct(
        public readonly int $userId,
        public readonly array $roles,
    ) {}
}
