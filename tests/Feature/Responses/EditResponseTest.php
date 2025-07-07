<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\EditProduct;
use Workbench\App\Models\Product;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->update = route('products.update', $this->product);

    $this->response = new EditProduct($this->product, $this->update);
});

it('has props', function () {
    expect($this->response)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(4)
            ->toHaveKeys([
                EditProduct::TITLE_PROP,
                EditProduct::HEAD_PROP,
                EditProduct::UPDATE_PROP,
                'product',
            ])
            ->{EditProduct::TITLE_PROP}->toBeNull()
            ->{EditProduct::HEAD_PROP}->toBeNull()
            ->{EditProduct::UPDATE_PROP}->toBe($this->update)
            ->{'product'}->toBe($this->product)
        );
});

it('is inertia response', function () {
    $is = 'Edit';

    get(route('products.edit', $this->product))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where('title', $is)
            ->where('head', $is)
            ->where('update', $this->update)
            ->has('product')
        );
});
