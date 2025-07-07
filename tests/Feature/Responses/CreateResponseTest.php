<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\CreateProduct;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->store = route('products.store');

    $this->response = (new CreateProduct($this->store));
});

it('has props', function () {
    expect($this->response)
        ->toProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(3)
            ->toHaveKeys([
                CreateProduct::TITLE_PROP,
                CreateProduct::HEAD_PROP,
                CreateProduct::STORE_PROP,
            ])
            ->{CreateProduct::TITLE_PROP}->toBeNull()
            ->{CreateProduct::HEAD_PROP}->toBeNull()
            ->{CreateProduct::STORE_PROP}->toBe($this->store)
        );
});

it('is inertia response', function () {
    $is = 'Create';

    get(route('products.create'))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where(CreateProduct::TITLE_PROP, $is)
            ->where(CreateProduct::HEAD_PROP, $is)
            ->where(CreateProduct::STORE_PROP, $this->store)
        );
});
