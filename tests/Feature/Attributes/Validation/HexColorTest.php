<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Workbench\App\Data\ColorData;

beforeEach(function () {});

it('validates hex color', function () {
    expect(fn () => ColorData::validateAndCreate(['color' => 'red']))
        ->toThrow(ValidationException::class);

    expect(fn () => ColorData::validateAndCreate(['color' => '#000000']))
        ->not->toThrow(ValidationException::class);
});
