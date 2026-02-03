<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();

        $this->command->info('Roles e permissões criadas com sucesso!');
    }

    private function createPermissions(): void
    {
        $this->command->info('Criando permissões...');

        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission->value, 'guard_name' => 'web'],
                [
                    'description' => $permission->label(),
                    'module' => $permission->module(),
                ]
            );
        }

        $this->command->info(sprintf('  %d permissões criadas/atualizadas', count(PermissionEnum::cases())));
    }

    private function createRoles(): void
    {
        $this->command->info('Criando roles...');

        // Super Admin - tem todas as permissões
        $superAdmin = Role::firstOrCreate(
            ['name' => RoleEnum::SUPER_ADMIN->value, 'guard_name' => 'web'],
            ['description' => RoleEnum::SUPER_ADMIN->description()]
        );
        $superAdmin->givePermissionTo(Permission::all());
        $this->command->info("  - {$superAdmin->name}: todas as permissões");

        // Admin - gerencia usuários e configurações
        $admin = Role::firstOrCreate(
            ['name' => RoleEnum::ADMIN->value, 'guard_name' => 'web'],
            ['description' => RoleEnum::ADMIN->description()]
        );
        $adminPermissions = [
            // Usuários
            PermissionEnum::USERS_VIEW->value,
            PermissionEnum::USERS_CREATE->value,
            PermissionEnum::USERS_UPDATE->value,
            PermissionEnum::USERS_DELETE->value,
            PermissionEnum::USERS_MANAGE_ROLES->value,
            // Roles
            PermissionEnum::ROLES_VIEW->value,
            PermissionEnum::PERMISSIONS_VIEW->value,
            // Pedidos
            PermissionEnum::ORDERS_VIEW->value,
            PermissionEnum::ORDERS_VIEW_ALL->value,
            PermissionEnum::ORDERS_CREATE->value,
            PermissionEnum::ORDERS_UPDATE->value,
            PermissionEnum::ORDERS_DELETE->value,
            PermissionEnum::ORDERS_CANCEL->value,
            PermissionEnum::ORDERS_APPROVE->value,
            // Produtos
            PermissionEnum::PRODUCTS_VIEW->value,
            PermissionEnum::PRODUCTS_CREATE->value,
            PermissionEnum::PRODUCTS_UPDATE->value,
            PermissionEnum::PRODUCTS_DELETE->value,
            // Clientes
            PermissionEnum::CUSTOMERS_VIEW->value,
            PermissionEnum::CUSTOMERS_CREATE->value,
            PermissionEnum::CUSTOMERS_UPDATE->value,
            PermissionEnum::CUSTOMERS_DELETE->value,
            // Relatórios
            PermissionEnum::REPORTS_VIEW->value,
            PermissionEnum::REPORTS_EXPORT->value,
            PermissionEnum::REPORTS_FINANCIAL->value,
            // Configurações
            PermissionEnum::SETTINGS_VIEW->value,
            PermissionEnum::SETTINGS_UPDATE->value,
            // Integrações
            PermissionEnum::INTEGRATIONS_VIEW->value,
            // Logs
            PermissionEnum::LOGS_VIEW->value,
            PermissionEnum::AUDIT_VIEW->value,
        ];
        $admin->syncPermissions($adminPermissions);
        $this->command->info("  - {$admin->name}: " . count($adminPermissions) . " permissões");

        // Operador - gestão de pedidos e atendimento
        $operador = Role::firstOrCreate(
            ['name' => RoleEnum::OPERADOR->value, 'guard_name' => 'web'],
            ['description' => RoleEnum::OPERADOR->description()]
        );
        $operadorPermissions = [
            // Pedidos
            PermissionEnum::ORDERS_VIEW->value,
            PermissionEnum::ORDERS_VIEW_ALL->value,
            PermissionEnum::ORDERS_CREATE->value,
            PermissionEnum::ORDERS_UPDATE->value,
            PermissionEnum::ORDERS_CANCEL->value,
            // Produtos
            PermissionEnum::PRODUCTS_VIEW->value,
            // Clientes
            PermissionEnum::CUSTOMERS_VIEW->value,
            PermissionEnum::CUSTOMERS_CREATE->value,
            PermissionEnum::CUSTOMERS_UPDATE->value,
            // Relatórios
            PermissionEnum::REPORTS_VIEW->value,
        ];
        $operador->syncPermissions($operadorPermissions);
        $this->command->info("  - {$operador->name}: " . count($operadorPermissions) . " permissões");

        // Cliente - acesso básico
        $cliente = Role::firstOrCreate(
            ['name' => RoleEnum::CLIENTE->value, 'guard_name' => 'web'],
            ['description' => RoleEnum::CLIENTE->description()]
        );
        $clientePermissions = [
            PermissionEnum::ORDERS_VIEW->value,
            PermissionEnum::ORDERS_CREATE->value,
            PermissionEnum::PRODUCTS_VIEW->value,
        ];
        $cliente->syncPermissions($clientePermissions);
        $this->command->info("  - {$cliente->name}: " . count($clientePermissions) . " permissões");
    }
}
