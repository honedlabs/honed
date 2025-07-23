<?php

declare(strict_types=1);

use Honed\Honed\Data\ActionData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

beforeEach(function () {

})->only();

it('validates existence', function (array $input) {
    ActionData::validateAndCreate([]);
})->throws(ValidationException::class)
    ->with([
        'empty' => [[]],
        'all' => [['all' => true]],
        'only' => [['only' => [1]]],
        'except' => [['except' => [1]]],
        'all-only' => [['all' => true, 'only' => [1]]],
        'all-except' => [['all' => true, 'except' => [1]]],
        'only-all' => [['only' => [1], 'all' => true]],
        'except-all' => [['except' => [1], 'all' => true]],
        'only-except' => [['only' => [1], 'except' => [1]]],
        'except-only' => [['except' => [1], 'only' => [1]]],
    ]);

it('validates all type', function (mixed $value) {
    ActionData::validateAndCreate([
        'all' => $value,
        'only' => [],
        'except' => [],
    ]);

})->throws(ValidationException::class)
    ->with([
        'string' => ['string'],
        'int' => [1],
        'array' => [[1]],
        'object' => [new stdClass],
    ]);

it('validates arrays', function (mixed $value) {
    ActionData::validateAndCreate([
        'all' => true,
        'only' => $value,
        'except' => $value,
    ]);
})->throws(ValidationException::class)
    ->with([
        'int' => [5],
        'string' => ['string'],
        'object' => [new stdClass],
        'nested object' => [[new stdClass]],
    ]);

it('validates', function (mixed $value) {
    $data = ActionData::validateAndCreate([
        'all' => true,
        'only' => $value,
        'except' => $value,
    ]);

    expect($data)
        ->all->toBeTrue()
        ->only->toBe($value)
        ->except->toBe($value);
})->with([
    'int' => [[1]],
    'string' => [['string']],
]);