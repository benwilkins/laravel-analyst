<?php

namespace Benwilkins\Analyst;

use Illuminate\Support\ServiceProvider;

/**
 * Class AnalystServiceProvider
 * @package Benwilkins\Analyst
 */
class AnalystServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-analyst.php' => config_path('laravel-analyst.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias(Analyst::class, 'laravel-analyst');
    }
}
