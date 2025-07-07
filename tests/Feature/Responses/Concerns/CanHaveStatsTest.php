<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Models\Product;
use Workbench\App\Overviews\ProductOverview;

beforeEach(function () {
    $product = Product::factory()->create();

    $this->response = new ShowProduct($product);
});

it('has no stats by default', function () {
    expect($this->response)
        ->getStats()->toBeNull();
});

it('has stats from class string', function () {
    expect($this->response)
        ->stats(ProductOverview::class)->toBe($this->response)
        ->getStats()->toBeInstanceOf(ProductOverview::class);
});

it('has stats from instance', function () {
    expect($this->response)
        ->stats(ProductOverview::make())->toBe($this->response)
        ->getStats()->toBeInstanceOf(ProductOverview::class);
});

it('has stats from model', function () {
    expect($this->response)
        ->stats()->toBe($this->response)
        ->getStats()->toBeInstanceOf(ProductOverview::class);
});

it('has stats props', function () {
    expect($this->response)
        ->canHaveStatsToProps()->toBe([])
        ->stats(ProductOverview::make())->toBe($this->response)
        ->canHaveStatsToProps()
        ->scoped(fn ($stats) => $stats
            ->toBeArray()
            ->toHaveCount(2)
            ->toHaveKeys([
                '_values',
                '_stat_key',
            ])
        );
});
