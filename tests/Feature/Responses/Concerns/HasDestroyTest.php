<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\DeleteProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->destroy = route('products.destroy', $this->product);

    $this->response = new DeleteProduct($this->product, $this->destroy);
});

it('has destroy url', function () {
    expect($this->response)
        ->getDestroy()->toBe($this->destroy)
        ->destroy('/products')->toBe($this->response)
        ->getDestroy()->toBe('/products');
});

it('has destroy props', function () {
    expect($this->response)
        ->hasDestroyToProps()
        ->scoped(fn ($destroy) => $destroy
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(DeleteProduct::DESTROY_PROP)
            ->{DeleteProduct::DESTROY_PROP}->toBe($this->destroy)
        );
});
