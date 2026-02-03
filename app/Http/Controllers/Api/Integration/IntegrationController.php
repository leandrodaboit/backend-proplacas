<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use App\Models\IntegrationToken;
use App\Services\Integration\IntegrationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntegrationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private IntegrationService $integrationService,
    ) {}

    public function webhook(Request $request): JsonResponse
    {
        $integration = $this->getIntegration($request);

        Log::channel('daily')->info('Webhook received', [
            'integration' => $integration->name,
            'type' => $integration->type,
            'payload' => $request->all(),
        ]);

        return $this->success([
            'received' => true,
            'integration' => $integration->name,
            'timestamp' => now()->toIso8601String(),
        ], 'Webhook processado com sucesso');
    }

    public function whatsappWebhook(Request $request): JsonResponse
    {
        $integration = $this->getIntegration($request);

        Log::channel('daily')->info('WhatsApp webhook received', [
            'integration' => $integration->name,
            'payload' => $request->all(),
        ]);

        return $this->success([
            'received' => true,
            'source' => 'whatsapp',
            'timestamp' => now()->toIso8601String(),
        ], 'WhatsApp webhook processado');
    }

    public function aiCallback(Request $request): JsonResponse
    {
        $integration = $this->getIntegration($request);

        Log::channel('daily')->info('AI callback received', [
            'integration' => $integration->name,
            'payload' => $request->all(),
        ]);

        return $this->success([
            'received' => true,
            'source' => 'ai',
            'timestamp' => now()->toIso8601String(),
        ], 'IA callback processado');
    }

    public function healthCheck(Request $request): JsonResponse
    {
        $integration = $this->getIntegration($request);

        return $this->success([
            'status' => 'healthy',
            'integration' => $integration->name,
            'stats' => $this->integrationService->getStats($integration),
            'timestamp' => now()->toIso8601String(),
        ], 'Integração funcionando');
    }

    private function getIntegration(Request $request): IntegrationToken
    {
        return $request->attributes->get('integration');
    }
}
