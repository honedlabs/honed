<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\EditProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->update = route('products.update', $this->product);

    $this->response = new EditProduct($this->product, $this->update);
});

it('has update url', function () {
    expect($this->response)
        ->getUpdate()->toBe($this->update)
        ->update('/products')->toBe($this->response)
        ->getUpdate()->toBe('/products');
});

it('has update props', function () {
    expect($this->response)
        ->hasUpdateToProps()
        ->scoped(fn ($update) => $update
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(EditProduct::UPDATE_PROP)
            ->{EditProduct::UPDATE_PROP}->toBe($this->update)
        );
});
