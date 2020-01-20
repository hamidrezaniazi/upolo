<?php

namespace Hamidrezaniazi\Upolo;

use Illuminate\Support\ServiceProvider;

class UpoloServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
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
        $this->app->singleton('upolo', function () {
            return new UpoloSkeleton();
        });
    }
}
