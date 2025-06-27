<?php

namespace App\Providers;

use App\Contracts\User\IUser;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class KeyCloakServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IUser::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
