<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Illuminate\Support\ServiceProvider;

class CrumbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/crumb.php', 'crumb');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/crumb.php' => config_path('crumb.php'),
        ], 'crumbs-config');

        $this->registerCrumbs();
    }

    /**
     * Register the crumbs.
     */
    protected function registerCrumbs(): void
    {
        $files = config('crumb.files');

        if (!$files || !is_file($files)) {
            return;
        }


        foreach ((array)$files as $file) {
            require $file;
        }
    }
}
