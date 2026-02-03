<?php

namespace App\Http\Middleware;

use App\Services\Integration\IntegrationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyIntegrationToken
{
    public function __construct(
        protected IntegrationService $integrationService
    ) {}

    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $startTime = microtime(true);

        $token = $this->extractToken($request);

        if (!$token) {
            return $this->unauthorizedResponse('Token de integração não fornecido');
        }

        $integration = $this->integrationService->findActiveByToken($token);

        if (!$integration) {
            $this->logFailedAttempt($request, $token);
            return $this->unauthorizedResponse('Token de integração inválido');
        }

        if (!$integration->isValid()) {
            return $this->unauthorizedResponse('Token de integração expirado ou inativo');
        }

        if (!$integration->isIpAllowed($request->ip())) {
            $this->logBlockedIp($request, $integration);
            return $this->forbiddenResponse('IP não autorizado para esta integração');
        }

        if ($ability && !$integration->hasAbility($ability)) {
            return $this->forbiddenResponse('Token não possui permissão para esta operação');
        }

        if ($this->isRateLimited($integration, $request)) {
            return $this->rateLimitedResponse();
        }

        $request->attributes->set('integration', $integration);

        $response = $next($request);

        $this->logRequest($request, $integration, $response, $startTime);

        return $response;
    }

    protected function extractToken(Request $request): ?string
    {
        if ($header = $request->header('X-Integration-Token')) {
            return $header;
        }

        if ($bearer = $request->bearerToken()) {
            if (str_starts_with($bearer, 'int_')) {
                return substr($bearer, 4);
            }
        }

        return $request->header('X-API-Key');
    }

    protected function isRateLimited($integration, Request $request): bool
    {
        $key = "integration_rate_limit:{$integration->id}";
        $attempts = Cache::get($key, 0);

        if ($attempts >= $integration->rate_limit_per_minute) {
            Log::channel('daily')->warning('Rate limit excedido', [
                'integration_id' => $integration->id,
                'integration_name' => $integration->name,
                'ip' => $request->ip(),
                'attempts' => $attempts,
            ]);
            return true;
        }

        Cache::put($key, $attempts + 1, now()->addMinute());

        return false;
    }

    protected function logRequest(Request $request, $integration, Response $response, float $startTime): void
    {
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        $this->integrationService->logRequest($integration, [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'response_status' => $response->getStatusCode(),
            'response_time_ms' => $responseTime,
            'request_headers' => $this->sanitizeHeaders($request->headers->all()),
            'request_body' => $this->sanitizeBody($request->all()),
        ]);
    }

    protected function logFailedAttempt(Request $request, string $token): void
    {
        Log::channel('daily')->warning('Token de integração inválido', [
            'ip' => $request->ip(),
            'endpoint' => $request->path(),
            'token_prefix' => substr($token, 0, 10) . '...',
        ]);
    }

    protected function logBlockedIp(Request $request, $integration): void
    {
        Log::channel('daily')->warning('IP bloqueado para integração', [
            'integration_id' => $integration->id,
            'integration_name' => $integration->name,
            'ip' => $request->ip(),
            'allowed_ips' => $integration->allowed_ips,
        ]);
    }

    protected function sanitizeHeaders(array $headers): array
    {
        $sensitive = ['authorization', 'x-integration-token', 'x-api-key', 'cookie'];

        foreach ($sensitive as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['[REDACTED]'];
            }
        }

        return $headers;
    }

    protected function sanitizeBody(array $body): array
    {
        $sensitive = ['password', 'password_confirmation', 'token', 'secret', 'api_key'];

        foreach ($sensitive as $field) {
            if (isset($body[$field])) {
                $body[$field] = '[REDACTED]';
            }
        }

        return $body;
    }

    protected function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => new \stdClass(),
        ], 401);
    }

    protected function forbiddenResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => new \stdClass(),
        ], 403);
    }

    protected function rateLimitedResponse(): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'Limite de requisições excedido. Tente novamente em breve.',
            'data' => new \stdClass(),
        ], 429);
    }
}
