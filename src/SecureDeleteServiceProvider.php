<?php

namespace Boreistudio\SecureDelete;

use Illuminate\Support\ServiceProvider;

class SecureDeleteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/secure-delete.php' => config_path('secure-delete.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'migrations');

            $this->commands([
                Commands\InstallSecureDeleteCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/secure-delete.php', 'secure-delete'
        );
    }
}