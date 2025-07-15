<?php

declare(strict_types=1);

namespace Honed\Honed;

use Honed\Honed\Commands\InertiaResponseMakeCommand;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class HonedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerMacros();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->offerPublishing();

            $this->commands([
                InertiaResponseMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the application's macros.
     */
    protected function registerMacros(): void
    {
        Blueprint::macro('authorable', function (
            string $createdBy = 'created_by',
            string $updatedBy = 'updated_by',
            string $table = 'users',
        ) {
            /** @var Blueprint $this */
            $this->foreignId($createdBy)->nullable()->constrained($table);
            $this->foreignId($updatedBy)->nullable()->constrained($table);
        });
    }

    /**
     * Register the publishing for the package.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'honed-stubs');
    }
}
