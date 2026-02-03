<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Integration\IntegrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rotas da API REST do Proplacas.
| Todas as respostas seguem o padrão: { success, message, data }
|
*/

/*
|--------------------------------------------------------------------------
| Rotas Públicas (sem autenticação)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('auth.register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('auth.login');
});

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas (usuários via Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');

        Route::post('/logout-all', [AuthController::class, 'logoutAll'])
            ->name('auth.logout-all');

        Route::get('/me', [AuthController::class, 'me'])
            ->name('auth.me');

        Route::get('/tokens', [AuthController::class, 'tokens'])
            ->name('auth.tokens');

        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken'])
            ->name('auth.tokens.revoke');
    });
});

/*
|--------------------------------------------------------------------------
| Rotas de Integrações Externas (WhatsApp, IA, Webhooks)
|--------------------------------------------------------------------------
|
| Protegidas pelo middleware VerifyIntegrationToken.
| Autenticação via header: X-Integration-Token ou X-API-Key
|
*/
Route::prefix('integrations')->middleware('integration.verify')->group(function () {
    Route::post('/webhook', [IntegrationController::class, 'webhook'])
        ->name('integrations.webhook');

    Route::post('/whatsapp/webhook', [IntegrationController::class, 'whatsappWebhook'])
        ->name('integrations.whatsapp.webhook');

    Route::post('/ai/callback', [IntegrationController::class, 'aiCallback'])
        ->name('integrations.ai.callback');

    Route::get('/health', [IntegrationController::class, 'healthCheck'])
        ->name('integrations.health');
});

/*
|--------------------------------------------------------------------------
| Health Check da API
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando',
        'data' => [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
        ],
    ]);
})->name('api.health');
