<?php

namespace App\Console\Commands;

use App\Services\Integration\IntegrationService;
use Illuminate\Console\Command;

class ListIntegrationTokens extends Command
{
    protected $signature = 'integration:list
                            {--type= : Filtrar por tipo}
                            {--active : Mostrar apenas ativos}';

    protected $description = 'Lista todos os tokens de integração';

    public function handle(IntegrationService $service): int
    {
        $integrations = $this->option('active')
            ? $service->listActive()
            : $service->list($this->option('type'));

        if ($integrations->isEmpty()) {
            $this->info('Nenhum token de integração encontrado.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Nome', 'Slug', 'Tipo', 'Status', 'Rate Limit', 'Último Uso', 'Expira'],
            $integrations->map(fn ($i) => [
                $i->id,
                $i->name,
                $i->slug,
                $i->type,
                $i->is_active ? '✓ Ativo' : '✗ Inativo',
                $i->rate_limit_per_minute . '/min',
                $i->last_used_at?->diffForHumans() ?? 'Nunca',
                $i->expires_at?->format('d/m/Y') ?? 'Nunca',
            ])
        );

        return self::SUCCESS;
    }
}
