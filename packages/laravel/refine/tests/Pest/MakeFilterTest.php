<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Refiners'));
});

it('makes filters', function () {
    $this->artisan('make:filter', [
        'name' => 'DateFilter',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Refiners/DateFilter.php'));
});

it('prompts for a filter name', function () {
    $this->artisan('make:filter')
        ->expectsQuestion('What should the filter be named?', 'DateFilter')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Refiners/DateFilter.php'));
});