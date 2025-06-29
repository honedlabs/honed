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
        ->getProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toBeEmpty()
        )
        ->table(ProductTable::class)
        ->getProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(IndexProduct::TABLE_PROP)
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
