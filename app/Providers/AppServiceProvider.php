<?php

namespace App\Providers;

use App\Repositories\Interfaces\IntegrationLogRepositoryInterface;
use App\Repositories\Interfaces\IntegrationTokenRepositoryInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\IntegrationLogRepository;
use App\Repositories\IntegrationTokenRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        IntegrationTokenRepositoryInterface::class => IntegrationTokenRepository::class,
        IntegrationLogRepositoryInterface::class => IntegrationLogRepository::class,
        RoleRepositoryInterface::class => RoleRepository::class,
        PermissionRepositoryInterface::class => PermissionRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Super admin tem acesso a tudo
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
