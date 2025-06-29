<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\CreateProduct;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->store = route('products.store');

    $this->response = new CreateProduct($this->store);
});

it('has store url', function () {
    expect($this->response)
        ->getStoreUrl()->toBe($this->store)
        ->storeUrl('/products')->toBe($this->response)
        ->getStoreUrl()->toBe('/products');
});

it('has props', function () {
    expect($this->response)
        ->getProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(CreateProduct::STORE_PROP)
        );
});

it('is ineertia response', function () {
    $is = 'Create';

    get(route('products.create'))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where('title', $is)
            ->where('head', $is)
            ->where('store', $this->store)
        );
});
