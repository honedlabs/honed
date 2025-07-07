<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\IndexProduct;
use Workbench\App\Tables\ProductTable;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->response = new IndexProduct();
});

it('has props', function () {
    expect($this->response)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(2)
            ->toHaveKeys([
                IndexProduct::TITLE_PROP,
                IndexProduct::HEAD_PROP,
            ])
            ->{IndexProduct::TITLE_PROP}->toBeNull()
            ->{IndexProduct::HEAD_PROP}->toBeNull()
        )
        ->table(ProductTable::class)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(3)
            ->toHaveKeys([
                IndexProduct::TITLE_PROP,
                IndexProduct::HEAD_PROP,
                IndexProduct::TABLE_PROP,
            ])
            ->{IndexProduct::TITLE_PROP}->toBeNull()
            ->{IndexProduct::HEAD_PROP}->toBeNull()
            ->{IndexProduct::TABLE_PROP}->toBeArray()
        );
});

it('is ineertia response', function () {
    $is = 'Index';

    get(route('products.index'))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where('title', $is)
            ->where('head', $is)
        );
});
