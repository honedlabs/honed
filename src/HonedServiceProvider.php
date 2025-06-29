<?php

declare(strict_types=1);

namespace Honed\Honed;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class HonedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMacros();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register the application's macros.
     *
     * @return void
     */
    protected function registerMacros()
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
}
