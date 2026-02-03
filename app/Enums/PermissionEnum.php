<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Usuários
    case USERS_VIEW = 'users.view';
    case USERS_CREATE = 'users.create';
    case USERS_UPDATE = 'users.update';
    case USERS_DELETE = 'users.delete';
    case USERS_MANAGE_ROLES = 'users.manage-roles';

    // Roles e Permissões
    case ROLES_VIEW = 'roles.view';
    case ROLES_CREATE = 'roles.create';
    case ROLES_UPDATE = 'roles.update';
    case ROLES_DELETE = 'roles.delete';
    case PERMISSIONS_VIEW = 'permissions.view';
    case PERMISSIONS_MANAGE = 'permissions.manage';

    // Pedidos
    case ORDERS_VIEW = 'orders.view';
    case ORDERS_VIEW_ALL = 'orders.view-all';
    case ORDERS_CREATE = 'orders.create';
    case ORDERS_UPDATE = 'orders.update';
    case ORDERS_DELETE = 'orders.delete';
    case ORDERS_CANCEL = 'orders.cancel';
    case ORDERS_APPROVE = 'orders.approve';

    // Produtos
    case PRODUCTS_VIEW = 'products.view';
    case PRODUCTS_CREATE = 'products.create';
    case PRODUCTS_UPDATE = 'products.update';
    case PRODUCTS_DELETE = 'products.delete';

    // Clientes
    case CUSTOMERS_VIEW = 'customers.view';
    case CUSTOMERS_CREATE = 'customers.create';
    case CUSTOMERS_UPDATE = 'customers.update';
    case CUSTOMERS_DELETE = 'customers.delete';

    // Relatórios
    case REPORTS_VIEW = 'reports.view';
    case REPORTS_EXPORT = 'reports.export';
    case REPORTS_FINANCIAL = 'reports.financial';

    // Configurações
    case SETTINGS_VIEW = 'settings.view';
    case SETTINGS_UPDATE = 'settings.update';

    // Integrações
    case INTEGRATIONS_VIEW = 'integrations.view';
    case INTEGRATIONS_CREATE = 'integrations.create';
    case INTEGRATIONS_UPDATE = 'integrations.update';
    case INTEGRATIONS_DELETE = 'integrations.delete';

    // Logs e Auditoria
    case LOGS_VIEW = 'logs.view';
    case AUDIT_VIEW = 'audit.view';

    public function label(): string
    {
        return match ($this) {
            // Usuários
            self::USERS_VIEW => 'Visualizar usuários',
            self::USERS_CREATE => 'Criar usuários',
            self::USERS_UPDATE => 'Editar usuários',
            self::USERS_DELETE => 'Excluir usuários',
            self::USERS_MANAGE_ROLES => 'Gerenciar roles de usuários',

            // Roles e Permissões
            self::ROLES_VIEW => 'Visualizar roles',
            self::ROLES_CREATE => 'Criar roles',
            self::ROLES_UPDATE => 'Editar roles',
            self::ROLES_DELETE => 'Excluir roles',
            self::PERMISSIONS_VIEW => 'Visualizar permissões',
            self::PERMISSIONS_MANAGE => 'Gerenciar permissões',

            // Pedidos
            self::ORDERS_VIEW => 'Visualizar próprios pedidos',
            self::ORDERS_VIEW_ALL => 'Visualizar todos os pedidos',
            self::ORDERS_CREATE => 'Criar pedidos',
            self::ORDERS_UPDATE => 'Editar pedidos',
            self::ORDERS_DELETE => 'Excluir pedidos',
            self::ORDERS_CANCEL => 'Cancelar pedidos',
            self::ORDERS_APPROVE => 'Aprovar pedidos',

            // Produtos
            self::PRODUCTS_VIEW => 'Visualizar produtos',
            self::PRODUCTS_CREATE => 'Criar produtos',
            self::PRODUCTS_UPDATE => 'Editar produtos',
            self::PRODUCTS_DELETE => 'Excluir produtos',

            // Clientes
            self::CUSTOMERS_VIEW => 'Visualizar clientes',
            self::CUSTOMERS_CREATE => 'Criar clientes',
            self::CUSTOMERS_UPDATE => 'Editar clientes',
            self::CUSTOMERS_DELETE => 'Excluir clientes',

            // Relatórios
            self::REPORTS_VIEW => 'Visualizar relatórios',
            self::REPORTS_EXPORT => 'Exportar relatórios',
            self::REPORTS_FINANCIAL => 'Relatórios financeiros',

            // Configurações
            self::SETTINGS_VIEW => 'Visualizar configurações',
            self::SETTINGS_UPDATE => 'Alterar configurações',

            // Integrações
            self::INTEGRATIONS_VIEW => 'Visualizar integrações',
            self::INTEGRATIONS_CREATE => 'Criar integrações',
            self::INTEGRATIONS_UPDATE => 'Editar integrações',
            self::INTEGRATIONS_DELETE => 'Excluir integrações',

            // Logs e Auditoria
            self::LOGS_VIEW => 'Visualizar logs',
            self::AUDIT_VIEW => 'Visualizar auditoria',
        };
    }

    public function module(): string
    {
        return match ($this) {
            self::USERS_VIEW, self::USERS_CREATE, self::USERS_UPDATE,
            self::USERS_DELETE, self::USERS_MANAGE_ROLES => 'users',

            self::ROLES_VIEW, self::ROLES_CREATE, self::ROLES_UPDATE,
            self::ROLES_DELETE, self::PERMISSIONS_VIEW, self::PERMISSIONS_MANAGE => 'access',

            self::ORDERS_VIEW, self::ORDERS_VIEW_ALL, self::ORDERS_CREATE,
            self::ORDERS_UPDATE, self::ORDERS_DELETE, self::ORDERS_CANCEL,
            self::ORDERS_APPROVE => 'orders',

            self::PRODUCTS_VIEW, self::PRODUCTS_CREATE, self::PRODUCTS_UPDATE,
            self::PRODUCTS_DELETE => 'products',

            self::CUSTOMERS_VIEW, self::CUSTOMERS_CREATE, self::CUSTOMERS_UPDATE,
            self::CUSTOMERS_DELETE => 'customers',

            self::REPORTS_VIEW, self::REPORTS_EXPORT, self::REPORTS_FINANCIAL => 'reports',

            self::SETTINGS_VIEW, self::SETTINGS_UPDATE => 'settings',

            self::INTEGRATIONS_VIEW, self::INTEGRATIONS_CREATE,
            self::INTEGRATIONS_UPDATE, self::INTEGRATIONS_DELETE => 'integrations',

            self::LOGS_VIEW, self::AUDIT_VIEW => 'logs',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function byModule(string $module): array
    {
        return array_filter(
            self::cases(),
            fn (self $permission) => $permission->module() === $module
        );
    }

    public static function modules(): array
    {
        return array_unique(array_map(
            fn (self $permission) => $permission->module(),
            self::cases()
        ));
    }

    public static function grouped(): array
    {
        $grouped = [];
        foreach (self::cases() as $permission) {
            $grouped[$permission->module()][] = [
                'value' => $permission->value,
                'label' => $permission->label(),
            ];
        }
        return $grouped;
    }
}
