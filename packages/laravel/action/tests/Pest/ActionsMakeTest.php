<?php

declare(strict_types=1);

use Honed\Action\Console\Commands\ActionsMakeCommand;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
    $this->artisan('make:model', ['name' => 'Product']);
    $this->actions = app(ActionsMakeCommand::class)->actions;
});

it('makes', function () {
    $this->artisan('make:actions', [
        'model' => 'Product',
    ])->assertSuccessful();

    foreach ($this->actions as $action) {
        $this->assertFileExists(app_path(\sprintf('Actions/Product/%sProduct.php', $action)));
    }
});

it('makes with path', function () {
    $this->artisan('make:actions', [
        'model' => 'Product',
        '--path' => '/Models/',
    ])->assertSuccessful();

    foreach ($this->actions as $action) {
        $this->assertFileExists(app_path(\sprintf('Actions/Models/Product/%sProduct.php', $action)));
    }

    $this->assertFileExists(app_path('Actions/Models/Product/ShowProduct.php'));
    
});

it('promps for missing model', function () {
    $this->artisan('make:actions')
        ->expectsQuestion('What is the name of the model?', 'Product')
        ->assertSuccessful();
});

