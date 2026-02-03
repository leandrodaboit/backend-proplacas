<?php

namespace App\DTO\Integration;

use App\Models\IntegrationToken;
use Spatie\LaravelData\Data;

class IntegrationResultDTO extends Data
{
    public function __construct(
        public readonly IntegrationToken $integration,
        public readonly string $token,
    ) {}
}
