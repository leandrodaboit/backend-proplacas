<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Como esta API é 100% stateless (sem cookies/sessões), deixamos
    | esta configuração vazia para não habilitar autenticação stateful.
    |
    */

    'stateful' => [],

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Guards de autenticação. Como não usamos sessões web, deixamos vazio
    | para forçar o uso exclusivo de Bearer Tokens.
    |
    */

    'guard' => [],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Tempo de expiração dos tokens em minutos.
    | null = tokens não expiram automaticamente
    | Recomendação: 60*24*30 = 43200 (30 dias) para apps mobile
    |
    */

    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 43200),

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Prefixo para tokens gerados. Útil para identificação em scanners
    | de segurança que detectam tokens vazados em repositórios.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'proplacas_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Middlewares do Sanctum. Como não usamos SPA stateful,
    | estes middlewares não serão utilizados.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
