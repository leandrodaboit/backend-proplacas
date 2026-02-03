<?php

namespace App\DTO\Permission;

use Spatie\LaravelData\Data;

class UpdateRoleDTO extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?array $permissions = null,
    ) {}
}
