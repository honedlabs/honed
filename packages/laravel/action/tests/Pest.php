<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Action\Tests\Stubs\Product;
use Honed\Action\Tests\Stubs\Status;
use Honed\Action\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

function product(?string $name = null): Product
{
    return Product::create([
        'public_id' => Str::uuid(),
        'name' => $name ?? fake()->unique()->word(),
        'description' => fake()->sentence(),
        'price' => fake()->randomNumber(4),
        'best_seller' => fake()->boolean(),
        'status' => fake()->randomElement(Status::cases()),
        'created_at' => now()->subDays(fake()->randomNumber(2)),
    ]);
}

function populate(int $count = 1000): void
{
    foreach (\range(1, $count) as $i) {
        product();
    }
}

/**
 * @return array{array<string,mixed>,array<string,mixed>}
 */
function params(Product $product): array
{
    $named = [
        'product' => $product,
    ];

    $typed = [
        Product::class => $product,
    ];

    return [$named, $typed];
}

/**
 * Get the testing actions.
 *
 * @return \Illuminate\Support\Collection<int,\Honed\Action\Action>
 */
function actions()
{
    return [
        InlineAction::make('update.name')
            ->action(fn ($product) => $product->update(['name' => 'test'])),

        InlineAction::make('show')
            ->route(fn ($product) => route('products.show', $product->public_id)),

        InlineAction::make('price.100')
            ->action(function ($product) {
                $product->update(['price' => 100]);

                return inertia('Show');
            }),

        InlineAction::make('update.description')
            ->action(fn ($product) => $product->update(['description' => 'test']))
            ->allow(false),

        BulkAction::make('update.name')
            ->action(fn ($product) => $product->update(['name' => 'test']))
            ->allow(false),

        BulkAction::make('update.description')
            ->action(fn ($product) => $product->update(['description' => 'test'])),

        BulkAction::make('price.50')
            ->action(function (Collection $products) {
                $products->each->update(['price' => 50]);

                return inertia('Show');
            }),

        PageAction::make('create.product.name')
            ->action(fn () => Product::create([
                'name' => 'name',
                'description' => 'name',
            ])),

        PageAction::make('create.product.description')
            ->action(fn () => Product::create([
                'name' => 'description',
                'description' => 'description',
            ]))
            ->allow(false),

        PageAction::make('create')
            ->route('products.create'),

        PageAction::make('price.10')
            ->action(function (Builder $builder) {
                $builder->update(['price' => 10]);

                return inertia('Show');
            }),
    ];
}
