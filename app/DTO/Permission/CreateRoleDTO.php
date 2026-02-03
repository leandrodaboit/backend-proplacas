<?php

namespace App\DTO\Permission;

use Spatie\LaravelData\Data;

class CreateRoleDTO extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly array $permissions = [],
        public readonly string $guardName = 'web',
    ) {}
}
