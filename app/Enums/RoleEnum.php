<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case OPERADOR = 'operador';
    case CLIENTE = 'cliente';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::OPERADOR => 'Operador',
            self::CLIENTE => 'Cliente',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Acesso total ao sistema, incluindo gerenciamento de permissões',
            self::ADMIN => 'Acesso administrativo com gerenciamento de usuários e configurações',
            self::OPERADOR => 'Acesso operacional para gestão de pedidos e atendimento',
            self::CLIENTE => 'Acesso básico para clientes do sistema',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_map(fn (self $role) => [
            'value' => $role->value,
            'label' => $role->label(),
            'description' => $role->description(),
        ], self::cases());
    }
}
