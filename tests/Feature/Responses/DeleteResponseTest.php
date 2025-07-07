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

it('has props', function () {
    expect($this->response)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(4)
            ->toHaveKeys([
                DeleteProduct::TITLE_PROP,
                DeleteProduct::HEAD_PROP,
                DeleteProduct::DESTROY_PROP,
                'product',
            ])
            ->{DeleteProduct::TITLE_PROP}->toBeNull()
            ->{DeleteProduct::HEAD_PROP}->toBeNull()
            ->{DeleteProduct::DESTROY_PROP}->toBe($this->destroy)
            ->{'product'}->toBe($this->product)
        );
});

it('is inertia response', function () {
    $is = 'Delete';

    get(route('products.delete', $this->product))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where(DeleteProduct::TITLE_PROP, $is)
            ->where(DeleteProduct::HEAD_PROP, $is)
            ->where(DeleteProduct::DESTROY_PROP, $this->destroy)
            ->has('product')
        );
});
