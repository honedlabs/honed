<?php

declare(strict_types=1);

use Workbench\App\Data\ProductData;
use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->response = new ShowProduct($this->product);
});

it('has model by default', function () {
    expect($this->response)
        ->getModel()->toBe($this->product);
});

it('sets data', function () {
    expect($this->response)
        ->data(ProductData::class)->toBe($this->response)
        ->getData()->toBe(ProductData::class);
});

it('sets prop name', function () {
    expect($this->response)
        ->getPropName()->toBe('product')
        ->propName('p')->toBe($this->response)
        ->getPropName()->toBe('p');
});

it('prepares model', function () {
    expect($this->response)
        ->getPropModel()->toBe($this->product)
        ->data(ProductData::class)->toBe($this->response)
        ->getPropModel()
        ->scoped(fn ($data) => $data
            ->toBeInstanceOf(ProductData::class)
            ->id->toBe($this->product->id)
            ->name->toBe($this->product->name)
        );
});
