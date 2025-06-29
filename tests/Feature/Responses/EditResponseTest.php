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

it('has update url', function () {
    expect($this->response)
        ->getUpdateUrl()->toBe($this->update)
        ->updateUrl('/products')->toBe($this->response)
        ->getUpdateUrl()->toBe('/products');
});

it('has props', function () {
    expect($this->response)
        ->getProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(2)
            ->toHaveKeys([
                EditProduct::UPDATE_PROP,
                'product',
            ])
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
