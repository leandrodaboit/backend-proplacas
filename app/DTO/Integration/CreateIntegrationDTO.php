<?php

namespace App\DTO\Integration;

use Spatie\LaravelData\Data;

class CreateIntegrationDTO extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $slug = null,
        public readonly ?string $description = null,
        public readonly array $abilities = [],
        public readonly ?array $allowedIps = null,
        public readonly int $rateLimitPerMinute = 60,
        public readonly ?string $expiresAt = null,
        public readonly bool $isActive = true,
    ) {}
}
