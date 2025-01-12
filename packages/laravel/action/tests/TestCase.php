<?php

declare(strict_types=1);

namespace Honed\Action\Tests;

use Inertia\Inertia;
use Honed\Action\Tests\Stubs\Status;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Honed\Action\ActionServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Inertia\ServiceProvider as InertiaServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/Stubs');
        Inertia::setRootView('app');
        config()->set('inertia.testing.ensure_pages_exist', false);
        config()->set('inertia.testing.page_paths', [realpath(__DIR__)]);

    }

    protected function getPackageProviders($app)
    {
        return [
            ActionServiceProvider::class,
            InertiaServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default(Status::Available->value);
            $table->unsignedInteger('price')->default(0);
            $table->boolean('best_seller')->default(false);
            $table->timestamps();
        });
    }

    protected function defineRoutes($router)
    {
        $router->middleware(SubstituteBindings::class)->group(function ($router) {
            $router->get('/', fn () => Inertia::render('Home'))->name('home.index');
            $router->get('/products', fn () => Inertia::render('Products/Index'))->name('product.index');
            $router->get('/products/{product}', fn () => Inertia::render('Products/Show'))->name('product.show');
            $router->get('/products/create', fn () => Inertia::render('Products/Create'))->name('product.create');
        });
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
