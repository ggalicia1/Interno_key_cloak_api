<?php

namespace App\Providers;

use App\Contracts\Client\IClient;
use App\Contracts\Client\IClientRoles;
use App\Contracts\User\IUser;
use App\Repository\Client\ClientRepository;
use App\Repository\Client\ClientRoleRepository;
use App\Repository\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class KeyCloakServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IUser::class, UserRepository::class);
        $this->app->bind(IClient::class, ClientRepository::class);
        $this->app->bind(IClientRoles::class, ClientRoleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
