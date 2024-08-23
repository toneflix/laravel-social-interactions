<?php

namespace ToneflixCode\SocialInteractions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @method \Illuminate\Support\Stringable replace(string|iterable $search, string|iterable $replace, $caseSensitive = true)
 * @method string toString()
 */
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
        if ($this->unmigrated('create_social_interactions_table') || $this->unmigrated('create_social_interactions_table')) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('social-interactions.php'),
            ], 'social-interactions-config');

            if ($this->unmigrated('create_social_interactions_table')) {
                $time = now()->format('Y_m_d_His');
                $this->publishes([
                    __DIR__ . '/../database/migrations/2024_06_30_213055_create_social_interactions_table.php' => database_path('migrations/' . $time . '_create_social_interactions_table.php'),
                ], 'social-interactions-migrations');
            }

            if ($this->unmigrated('create_social_interaction_saves_table')) {
                $time = now()->addSeconds(1)->format('Y_m_d_His');
                $this->publishes([
                    __DIR__ . '/../database/migrations/2024_06_30_213056_create_social_interaction_saves_table.php' => database_path('migrations/' . $time . '_create_social_interaction_saves_table.php'),
                ], 'social-interactions-migrations');
            }

            if ($this->unmigrated('update_social_interaction_saves_table_add_list_id')) {
                $time = now()->addSeconds(2)->format('Y_m_d_His');
                $this->publishes([
                    __DIR__ . '/../database/migrations/2024_07_10_215210_update_social_interaction_saves_table_add_list_id.php' => database_path('migrations/' . $time . '_update_social_interaction_saves_table_add_list_id.php'),
                ], 'social-interactions-migrations');
            }
        }
    }

    protected function unmigrated(string $table)
    {
        return empty(array_filter(
            File::files(base_path('database/migrations')),
            function (SplFileInfo $file) use ($table) {
                return str($file->getBasename())->contains($table);
            }
        ));
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if (! Stringable::hasMacro('pastTense')) {
            Stringable::macro('pastTense', function () {
                return $this->replace($this, Helpers::convertToPastTense($this->toString()));
            });
        }

        if (! Str::hasMacro('pastTense')) {
            Str::macro('pastTense', function () {
                return str($this)->pastTense();
            });
        }

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'social-interactions');

        // Register the main class to use with the facade
        $this->app->singleton('social-interactions', function () {
            return new SocialInteractions;
        });
    }
}
