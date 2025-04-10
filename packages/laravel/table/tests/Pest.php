<?php

use Honed\Table\Tests\Stubs\Category;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Seller;
use Honed\Table\Tests\Stubs\Status;
use Honed\Table\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

function product(?string $name = null): Product
{
    return seller()->products()->create([
        'public_id' => Str::uuid(),
        'name' => $name ?? fake()->unique()->words(2, true),
        'description' => fake()->sentence(),
        'price' => fake()->randomNumber(4),
        'best_seller' => fake()->boolean(),
        'status' => fake()->randomElement(Status::cases()),
        'created_at' => now()->subDays(fake()->randomNumber(2)),
    ]);
}

function category(?string $name = null): Category
{
    return Category::create([
        'name' => $name ?? fake()->unique()->word(),
    ]);
}

function populate(int $count = 100)
{
    foreach (\range(1, $count) as $i) {
        product();
    }
}

function seller(?string $name = null): Seller
{
    return Seller::create([
        'name' => $name ?? fake()->unique()->name(),
    ]);
}

function qualifyProduct(string $column)
{
    return Product::query()->qualifyColumn($column);
}

function searchSql(string $column)
{
    return \sprintf('LOWER(%s) LIKE ?', qualifyProduct($column));
}

expect()->extend('toBeOrder', function (string $column, string $direction = 'asc') {
    return $this->toBeArray()
        ->toHaveKeys(['column', 'direction'])
        ->{'column'}->toBe($column)
        ->{'direction'}->toBe($direction);
});

expect()->extend('toBeOnlyOrder', function (string $column, string $direction = 'asc') {
    return $this->toBeArray()
        ->toHaveCount(1)
        ->{0}->scoped(fn ($order) => $order
        ->toBeOrder($column, $direction)
        );
});
