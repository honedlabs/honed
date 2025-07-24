<?php

declare(strict_types=1);

use Workbench\App\Enums\Status;
use Workbench\App\Models\User;

test('enum_value', function () {
    expect(enum_value('value'))->toBe('value');

    expect(enum_value(Status::Available))->toBe('available');
});

test('attempt', function () {
    expect(attempt(fn () => 'value'))->toBe(['value', null]);
    expect(attempt(fn () => throw new Exception('error')))
        ->{0}->toBeNull()
        ->{1}->toBeInstanceOf(Exception::class);
});

test('getKey', function () {
    expect(getKey(1))->toBe(1);
    expect(getKey('1'))->toBe('1');
    expect(getKey($user = User::factory()->create()))->toBe($user->id);
});
