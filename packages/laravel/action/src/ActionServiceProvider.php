<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Console\Commands\ActionMakeCommand;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/action.php', 'action');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionMakeCommand::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/action.php' => config_path('action.php'),
        ], 'action-config');

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'action-stub');
    }

    /**
     * Get the services provided by the provider.
     * 
     * @return array<int, class-string>
     */
    public function provides(): array
    {
        return [
            ActionMakeCommand::class
        ];
    }
}
