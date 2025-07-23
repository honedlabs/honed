<?php

declare(strict_types=1);

use Workbench\App\Data\IdData;
use Workbench\App\Models\Product;
use Workbench\App\Data\ProductData;
use Workbench\App\Models\User;

it('converts to data explicitly', function () {
    $product = Product::factory()->create();

    expect($product->toData(IdData::class))
        ->toBeInstanceOf(IdData::class)
        ->id->toBe($product->id);
});

it('converts to data via attribute', function () {
    $product = Product::factory()->create();

    expect($product->toData())
        ->toBeInstanceOf(ProductData::class)
        ->id->toBe($product->id)
        ->name->toBe($product->name);
});

it('converts to data via property', function () {
    $user = User::factory()->create();

    expect($user->toData())
        ->toBeInstanceOf(IdData::class)
        ->id->toBe($user->id);
});

it('does not convert to data if no data class is specified', function () {
    $user = User::factory()->create();

    $user->removeDataClass();

    $user->toData();
})->throws(TypeError::class);