<?php

namespace App\Providers;

use App\Repositories\Interfaces\IntegrationLogRepositoryInterface;
use App\Repositories\Interfaces\IntegrationTokenRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\IntegrationLogRepository;
use App\Repositories\IntegrationTokenRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        IntegrationTokenRepositoryInterface::class => IntegrationTokenRepository::class,
        IntegrationLogRepositoryInterface::class => IntegrationLogRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
