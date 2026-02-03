<?php

namespace App\DTO\Auth;

use Spatie\LaravelData\Data;

class RegisterDTO extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $deviceName,
        public readonly string $ip,
        public readonly ?string $sobrenome = null,
        public readonly ?string $telefone = null,
        public readonly string $tipo = 'cliente',
    ) {}
}
