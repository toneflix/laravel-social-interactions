<?php

namespace ToneflixCode\SocialInteractions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class SocialInteractionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('social-interactions.php'),
            ], 'social-interactions-config');

            $check = fn (string $table) => empty(array_filter(
                File::files(base_path('database/migrations')),
                function (SplFileInfo $file) use ($table) {
                    return str($file->getBasename())->contains($table);
                }
            ));

            if ($check('create_social_interactions_table')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/2024_06_30_213055_create_social_interactions_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_social_interactions_table.php'),
                ], 'social-interactions-migrations');
            }

            if ($check('create_social_interaction_saves_table')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/2024_06_30_213055_create_social_interaction_saves_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_social_interaction_saves_table.php'),
                ], 'social-interactions-migrations');
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'social-interactions');

        // Register the main class to use with the facade
        $this->app->singleton('social-interactions', function () {
            return new SocialInteractions();
        });
    }
}
