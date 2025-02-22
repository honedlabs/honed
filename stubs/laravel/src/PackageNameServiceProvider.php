<?php

declare(strict_types=1);

namespace VendorName\PackageName;

use Illuminate\Support\ServiceProvider;

class PackageNameServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/:package_slug.php', ':package_slug');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/:package_slug.php' => config_path(':package_slug.php'),
        ], ':package_slug-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }
}