<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Infolists\ProductInfolist;
use Workbench\App\Models\Product;
use Workbench\App\Overviews\ProductOverview;
use Workbench\App\Tables\ProductTable;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->response = new ShowProduct($this->product);
});

it('has props with model', function () {
    expect($this->response)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(3)
            ->toHaveKeys([
                ShowProduct::TITLE_PROP,
                ShowProduct::HEAD_PROP,
                'product',
            ])
            ->{ShowProduct::TITLE_PROP}->toBeNull()
            ->{ShowProduct::HEAD_PROP}->toBeNull()
            ->{'product'}->toBe($this->product)
        );
});

it('has props with batch', function () {
    expect($this->response)
        ->batch()->toBe($this->response)
        ->getBatch()->toBeInstanceof(Batch::class)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(4)
            ->toHaveKeys([
                ShowProduct::TITLE_PROP,
                ShowProduct::HEAD_PROP,
                ShowProduct::BATCH_PROP,
                'product',
            ])
        );
});

it('has props with infolist', function () {
    expect($this->response)
        ->infolist()->toBe($this->response)
        ->getInfolist()->toBeInstanceof(ProductInfolist::class)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(4)
            ->toHaveKeys([
                ShowProduct::TITLE_PROP,
                ShowProduct::HEAD_PROP,
                ShowProduct::INFOLIST_PROP,
                'product',
            ])
            ->{ShowProduct::TITLE_PROP}->toBeNull()
            ->{ShowProduct::HEAD_PROP}->toBeNull()
            ->{ShowProduct::INFOLIST_PROP}->toBeArray()
            ->{'product'}->toBe($this->product)
        );
});

it('has props with table', function () {
    expect($this->response)
        ->table()->toBe($this->response)
        ->getTable()->toBeInstanceof(ProductTable::class)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(4)
            ->toHaveKeys([
                ShowProduct::TITLE_PROP,
                ShowProduct::HEAD_PROP,
                ShowProduct::TABLE_PROP,
                'product',
            ])
            ->{ShowProduct::TITLE_PROP}->toBeNull()
            ->{ShowProduct::HEAD_PROP}->toBeNull()
            ->{ShowProduct::TABLE_PROP}->toBeArray()
            ->{'product'}->toBe($this->product)
        );
});

it('has props with stats', function () {
    expect($this->response)
        ->stats()->toBe($this->response)
        ->getStats()->toBeInstanceof(ProductOverview::class)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(5)
            ->toHaveKeys([
                ShowProduct::TITLE_PROP,
                ShowProduct::HEAD_PROP,
                'product',
                '_values',
                '_stat_key',
            ])
            ->{ShowProduct::TITLE_PROP}->toBeNull()
            ->{ShowProduct::HEAD_PROP}->toBeNull()
            ->{'_values'}->toBe([])
            ->{'_stat_key'}->toBe(ProductOverview::PROP)
            ->{'product'}->toBe($this->product)
        );
});

it('is ineertia response', function () {
    $is = 'Show';

    get(route('products.show', $this->product))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where('title', $is)
            ->where('head', $is)
            ->has('product')
        );
});
