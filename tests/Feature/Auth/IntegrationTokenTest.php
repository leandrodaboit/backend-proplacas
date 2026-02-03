<?php

namespace Tests\Feature\Auth;

use App\DTO\Integration\CreateIntegrationDTO;
use App\Services\Integration\IntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTokenTest extends TestCase
{
    use RefreshDatabase;

    private function createIntegration(array $overrides = []): object
    {
        $dto = new CreateIntegrationDTO(
            name: $overrides['name'] ?? 'Test Integration',
            type: $overrides['type'] ?? 'webhook',
            allowedIps: $overrides['allowed_ips'] ?? null,
            isActive: $overrides['is_active'] ?? true,
            expiresAt: isset($overrides['expires_at']) ? $overrides['expires_at']->format('Y-m-d H:i:s') : null,
        );

        return app(IntegrationService::class)->create($dto);
    }

    public function test_integration_can_access_webhook_with_valid_token(): void
    {
        $result = $this->createIntegration();

        $response = $this->withHeader('X-Integration-Token', $result->token)
            ->postJson('/api/integrations/webhook', ['event' => 'test']);

        $response->assertStatus(200)->assertJson(['success' => true, 'data' => ['received' => true]]);
    }

    public function test_integration_cannot_access_without_token(): void
    {
        $response = $this->postJson('/api/integrations/webhook', ['event' => 'test']);

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Token de integração não fornecido']);
    }

    public function test_integration_cannot_access_with_invalid_token(): void
    {
        $response = $this->withHeader('X-Integration-Token', 'invalid-token')
            ->postJson('/api/integrations/webhook');

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Token de integração inválido']);
    }

    public function test_integration_cannot_access_with_inactive_token(): void
    {
        $result = $this->createIntegration(['is_active' => false]);

        $response = $this->withHeader('X-Integration-Token', $result->token)
            ->postJson('/api/integrations/webhook');

        $response->assertStatus(401);
    }

    public function test_integration_cannot_access_with_expired_token(): void
    {
        $result = $this->createIntegration(['expires_at' => now()->subDay()]);

        $response = $this->withHeader('X-Integration-Token', $result->token)
            ->postJson('/api/integrations/webhook');

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Token de integração inválido']);
    }

    public function test_integration_request_is_logged(): void
    {
        $result = $this->createIntegration();

        $this->withHeader('X-Integration-Token', $result->token)->postJson('/api/integrations/webhook');

        $this->assertDatabaseHas('integration_logs', [
            'integration_token_id' => $result->integration->id,
            'endpoint' => 'api/integrations/webhook',
            'method' => 'POST',
        ]);
    }

    public function test_integration_last_used_at_is_updated(): void
    {
        $result = $this->createIntegration();

        $this->assertNull($result->integration->last_used_at);

        $this->withHeader('X-Integration-Token', $result->token)->postJson('/api/integrations/webhook');

        $this->assertNotNull($result->integration->fresh()->last_used_at);
    }

    public function test_integration_can_access_via_x_api_key_header(): void
    {
        $result = $this->createIntegration();

        $response = $this->withHeader('X-API-Key', $result->token)->postJson('/api/integrations/webhook');

        $response->assertStatus(200);
    }

    public function test_integration_can_access_health_check(): void
    {
        $result = $this->createIntegration();

        $response = $this->withHeader('X-Integration-Token', $result->token)->getJson('/api/integrations/health');

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'data' => ['status' => 'healthy', 'integration' => 'Test Integration'],
        ]);
    }

    public function test_integration_ip_restriction(): void
    {
        $result = $this->createIntegration(['allowed_ips' => ['192.168.1.1']]);

        $response = $this->withHeader('X-Integration-Token', $result->token)->postJson('/api/integrations/webhook');

        $response->assertStatus(403)->assertJson(['success' => false, 'message' => 'IP não autorizado para esta integração']);
    }

    public function test_integration_service_can_regenerate_token(): void
    {
        $service = app(IntegrationService::class);
        $result = $this->createIntegration();
        $oldToken = $result->token;

        $newToken = $service->regenerateToken($result->integration);

        $this->assertNotEquals($oldToken, $newToken);
    }

    public function test_integration_service_can_deactivate(): void
    {
        $service = app(IntegrationService::class);
        $result = $this->createIntegration();

        $service->deactivate($result->integration);

        $this->assertFalse($result->integration->fresh()->is_active);
    }
}
