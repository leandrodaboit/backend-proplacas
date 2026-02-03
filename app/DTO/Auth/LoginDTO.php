<?php

namespace App\DTO\Auth;

use Spatie\LaravelData\Data;

class LoginDTO extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $deviceName,
        public readonly string $ip,
    ) {}
}
