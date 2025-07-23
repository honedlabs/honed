<?php

declare(strict_types=1);

use Honed\Honed\Data\InlineData;
use Illuminate\Validation\ValidationException;

it('validates existence', function () {
    InlineData::validateAndCreate([]);
})->throws(ValidationException::class);

it('validates type', function (mixed $value) {
    InlineData::validateAndCreate(['id' => $value]);
})->throws(ValidationException::class)
    ->with([
        'bool' => [true],
        'array' => [[1]],
        'object' => [new stdClass],
    ]);

it('validates format', function (mixed $value) {
    $data = InlineData::validateAndCreate(['id' => $value]);

    expect($data)
        ->id->toBe($value);
})->with([
    'int' => [1],
    'string' => ['string'],
]);