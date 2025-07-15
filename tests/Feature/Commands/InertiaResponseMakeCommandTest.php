<?php

declare(strict_types=1);

use Honed\Honed\Responses\CreateResponse;
use Honed\Honed\Responses\DeleteResponse;
use Honed\Honed\Responses\EditResponse;
use Honed\Honed\Responses\IndexResponse;
use Honed\Honed\Responses\ShowResponse;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Responses'));
});

afterEach(function () {
    File::cleanDirectory(app_path('Responses'));
});

it('makes parent response', function () {
    $this->artisan('make:inertia-response', [
        'name' => 'IndexUserResponse',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Responses/IndexUserResponse.php'));
});

it('asks for a name', function () {
    $this->artisan('make:inertia-response', [
        '--force' => true,
    ])->expectsQuestion('What should the response be named?', 'IndexUserResponse')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Responses/IndexUserResponse.php'));
});

it('makes types', function (string $type, string $name, string $class) {
    $this->artisan('make:inertia-response', [
        'name' => $name,
        '--type' => $type,
        '--force' => true,
    ])->assertSuccessful();

    $path = app_path('Responses/'.$name.'.php');

    $this->assertFileExists($path);
    $this->assertStringContainsString($class, File::get($path));
})->with([
    ['index', 'IndexUserResponse', IndexResponse::class],
    ['show', 'ShowUserResponse', ShowResponse::class],
    ['create', 'CreateUserResponse', CreateResponse::class],
    ['edit', 'EditUserResponse', EditResponse::class],
    ['delete', 'DeleteUserResponse', DeleteResponse::class],
]);
