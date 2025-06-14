<?php

declare(strict_types=1);

use Workbench\App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

it('formats abn when getting', function () {
    $abn = '12345678901';

    $user = User::factory()->create([
        'formatted_abn' => $abn,
    ]);

    expect($user)
        ->formatted_abn->toBe('12 345 678 901');

    assertDatabaseHas('users', [
        'formatted_abn' => $abn,
    ]);
});

it('does not cast nulls', function () {
    $user = User::factory()->create([
        'formatted_abn' => null,
    ]);

    expect($user)
        ->formatted_abn->toBeNull();

    assertDatabaseHas('users', [
        'formatted_abn' => null,
    ]);
});

it('removes whitespace when setting', function () {
    $abn = '12 345 678 901';

    $user = User::factory()->create([
        'formatted_abn' => $abn,
    ]);

    expect($user)
        ->formatted_abn->toBe('12 345 678 901');

    assertDatabaseHas('users', [
        'formatted_abn' => '12345678901',
    ]);
});
