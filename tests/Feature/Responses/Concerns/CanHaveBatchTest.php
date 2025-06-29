<?php

declare(strict_types=1);

use Workbench\App\Batches\ProductBatch;
use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $product = Product::factory()->create();

    $this->response = new ShowProduct($product);
});

it('has no batch by default', function () {
    expect($this->response)
        ->getBatch()->toBeNull();
});

it('has batch from class string', function () {
    expect($this->response)
        ->batch(ProductBatch::class)->toBe($this->response)
        ->getBatch()->toBeInstanceOf(ProductBatch::class);
});

it('has batch from instance', function () {
    expect($this->response)
        ->batch(ProductBatch::make())->toBe($this->response)
        ->getBatch()->toBeInstanceOf(ProductBatch::class);
});
