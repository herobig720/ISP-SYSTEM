<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/auth.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/settings.php');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
