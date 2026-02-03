<?php

namespace App\Console\Commands;

use App\Services\Permission\PermissionService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync
                            {--fresh : Limpa o cache antes de sincronizar}';

    protected $description = 'Sincroniza permissões do PermissionEnum com o banco de dados';

    public function handle(PermissionService $service): int
    {
        if ($this->option('fresh')) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $this->info('Cache de permissões limpo.');
        }

        $this->info('Sincronizando permissões...');

        $service->syncFromEnum();

        $this->info('Permissões sincronizadas com sucesso!');
        $this->newLine();

        // Mostra resumo
        $permissions = $service->list();
        $grouped = $service->getGrouped();

        $this->table(
            ['Módulo', 'Qtd. Permissões'],
            collect($grouped)->map(fn ($perms, $module) => [
                $module ?: 'Sem módulo',
                count($perms),
            ])->toArray()
        );

        $this->newLine();
        $this->info("Total: {$permissions->count()} permissões");

        return self::SUCCESS;
    }
}
