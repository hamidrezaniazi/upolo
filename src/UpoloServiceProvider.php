<?php

namespace Hamidrezaniazi\Upolo;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UpoloServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('upolo.php'),
            ], 'config');
        }

        if (!class_exists('CreateModelHistoriesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_files_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . 'create_files_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'upolo');

        // Register the main class to use with the facade
        $this->app->singleton('upolo', function () {
            return new Upolo();
        });
    }
}
