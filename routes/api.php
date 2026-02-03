<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Integration\IntegrationController;
use App\Http\Controllers\Api\Permission\PermissionController;
use App\Http\Controllers\Api\Permission\RoleController;
use App\Http\Controllers\Api\Permission\UserPermissionController;
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
    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Permissions - Minhas permissões
    |--------------------------------------------------------------------------
    */
    Route::get('/my-permissions', [UserPermissionController::class, 'me'])
        ->name('permissions.me');

    Route::post('/check-permission', [UserPermissionController::class, 'check'])
        ->name('permissions.check');

    /*
    |--------------------------------------------------------------------------
    | Roles (gerenciamento)
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('can:roles.view')
            ->name('roles.index');

        Route::get('/defaults', [RoleController::class, 'defaults'])
            ->middleware('can:roles.view')
            ->name('roles.defaults');

        Route::get('/{role}', [RoleController::class, 'show'])
            ->middleware('can:roles.view')
            ->name('roles.show');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('can:roles.create')
            ->name('roles.store');

        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('can:roles.update')
            ->name('roles.update');

        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('can:roles.delete')
            ->name('roles.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Permissions (listagem)
    |--------------------------------------------------------------------------
    */
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('can:permissions.view')
            ->name('permissions.index');

        Route::get('/grouped', [PermissionController::class, 'grouped'])
            ->middleware('can:permissions.view')
            ->name('permissions.grouped');

        Route::get('/modules', [PermissionController::class, 'modules'])
            ->middleware('can:permissions.view')
            ->name('permissions.modules');

        Route::get('/available', [PermissionController::class, 'available'])
            ->middleware('can:permissions.view')
            ->name('permissions.available');

        Route::get('/module/{module}', [PermissionController::class, 'byModule'])
            ->middleware('can:permissions.view')
            ->name('permissions.byModule');
    });

    /*
    |--------------------------------------------------------------------------
    | User Permissions (atribuição)
    |--------------------------------------------------------------------------
    */
    Route::prefix('users/{user}/permissions')->group(function () {
        Route::get('/', [UserPermissionController::class, 'show'])
            ->middleware('can:users.view')
            ->name('users.permissions.show');

        Route::post('/roles', [UserPermissionController::class, 'assignRoles'])
            ->middleware('can:users.manage-roles')
            ->name('users.permissions.assignRoles');

        Route::delete('/roles', [UserPermissionController::class, 'revokeRoles'])
            ->middleware('can:users.manage-roles')
            ->name('users.permissions.revokeRoles');

        Route::post('/direct', [UserPermissionController::class, 'assignPermissions'])
            ->middleware('can:permissions.manage')
            ->name('users.permissions.assignPermissions');

        Route::delete('/direct', [UserPermissionController::class, 'revokePermissions'])
            ->middleware('can:permissions.manage')
            ->name('users.permissions.revokePermissions');
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
