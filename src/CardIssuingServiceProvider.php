<?php

namespace Kwidoo\CardIssuing;

use Illuminate\Support\ServiceProvider;
use Kwidoo\CardIssuing\Models\Card;
use Kwidoo\CardIssuing\Observers\CardObserver;

class CardIssuingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'card-issuing');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'card-issuing');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('card-issuing.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/card-issuing'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/card-issuing'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/card-issuing'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
        Card::observe(CardObserver::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'card-issuing');

        // Register the main class to use with the facade
        $this->app->singleton('card-issuing', function () {
            return new CardIssuing;
        });
    }
}
