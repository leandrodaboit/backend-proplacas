<?php

namespace Database\Seeders;

use App\DTO\Integration\CreateIntegrationDTO;
use App\Services\Integration\IntegrationService;
use Illuminate\Database\Seeder;

class IntegrationTokenSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(IntegrationService::class);

        $integrations = [
            new CreateIntegrationDTO(
                name: 'WhatsApp Integration',
                type: 'whatsapp',
                slug: 'whatsapp',
                description: 'Token para integração com WhatsApp Business API',
                abilities: ['whatsapp:send', 'whatsapp:receive', 'webhook:receive'],
                rateLimitPerMinute: 120,
            ),
            new CreateIntegrationDTO(
                name: 'AI Service',
                type: 'ai',
                slug: 'ai-service',
                description: 'Token para serviços de IA e processamento',
                abilities: ['ai:process', 'ai:callback'],
                rateLimitPerMinute: 30,
            ),
            new CreateIntegrationDTO(
                name: 'General Webhook',
                type: 'webhook',
                slug: 'general-webhook',
                description: 'Token genérico para webhooks externos',
                abilities: ['webhook:receive'],
                rateLimitPerMinute: 60,
            ),
        ];

        foreach ($integrations as $dto) {
            $result = $service->create($dto);

            $this->command->info("Integration '{$dto->name}' created.");
            $this->command->warn("Token: {$result->token}");
            $this->command->newLine();
        }

        $this->command->alert('IMPORTANTE: Salve os tokens acima!');
    }
}
