<?php

namespace App\Console\Commands;

use App\DTO\Integration\CreateIntegrationDTO;
use App\Services\Integration\IntegrationService;
use Illuminate\Console\Command;

class CreateIntegrationToken extends Command
{
    protected $signature = 'integration:create
                            {name : Nome da integração}
                            {--type=external : Tipo (whatsapp, ai, webhook, external)}
                            {--description= : Descrição da integração}
                            {--rate-limit=60 : Limite de requisições por minuto}
                            {--expires= : Data de expiração (Y-m-d H:i:s)}';

    protected $description = 'Cria um novo token de integração';

    public function handle(IntegrationService $service): int
    {
        $type = $this->option('type');

        if (!in_array($type, ['whatsapp', 'ai', 'webhook', 'external'])) {
            $this->error('Tipo inválido. Use: whatsapp, ai, webhook, external');
            return self::FAILURE;
        }

        $dto = new CreateIntegrationDTO(
            name: $this->argument('name'),
            type: $type,
            description: $this->option('description'),
            rateLimitPerMinute: (int) $this->option('rate-limit'),
            expiresAt: $this->option('expires'),
        );

        $result = $service->create($dto);

        $this->info('Token de integração criado com sucesso!');
        $this->newLine();

        $this->table(['Campo', 'Valor'], [
            ['ID', $result->integration->id],
            ['Nome', $result->integration->name],
            ['Slug', $result->integration->slug],
            ['Tipo', $result->integration->type],
            ['Rate Limit', $result->integration->rate_limit_per_minute . '/min'],
            ['Status', $result->integration->is_active ? 'Ativo' : 'Inativo'],
        ]);

        $this->newLine();
        $this->warn('TOKEN (salve agora, não será exibido novamente):');
        $this->line($result->token);
        $this->newLine();

        return self::SUCCESS;
    }
}
