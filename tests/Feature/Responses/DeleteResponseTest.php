<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\DeleteProduct;
use Workbench\App\Models\Product;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->destroy = route('products.destroy', $this->product);

    $this->response = new DeleteProduct($this->product, $this->destroy);
});

it('has delete url', function () {
    expect($this->response)
        ->getDestroyUrl()->toBe($this->destroy)
        ->destroyUrl('/products')->toBe($this->response)
        ->getDestroyUrl()->toBe('/products');
});

it('has props', function () {
    expect($this->response)
        ->getProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(2)
            ->toHaveKeys([
                DeleteProduct::DESTROY_PROP,
                'product',
            ])
        );
});

it('is inertia response', function () {
    $is = 'Delete';

    get(route('products.delete', $this->product))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where('title', $is)
            ->where('head', $is)
            ->where('destroy', $this->destroy)
            ->has('product')
        );
});
