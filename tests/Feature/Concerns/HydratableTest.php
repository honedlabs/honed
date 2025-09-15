<?php

declare(strict_types=1);

use Workbench\App\Data\ProductData;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->data = ProductData::from($this->product);
});

it('hydrates data into a model', function () {
    expect($this->data->hydrate())
        ->toBeInstanceOf(Product::class)
        ->id->toBe($this->product->id)
        ->name->toBe($this->product->name);
});
