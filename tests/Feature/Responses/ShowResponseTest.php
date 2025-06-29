<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Workbench\App\Batches\ProductBatch;
use Workbench\App\Http\Responses\ShowProduct;
use Workbench\App\Models\Product;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->response = new ShowProduct($this->product);
});

it('has props with model', function () {
    expect($this->response->getProps())
        ->toBeArray()
        ->toHaveCount(1)
        ->toHaveKey('product');
});

it('has props with batch', function () {
    expect($this->response)
        ->batch(ProductBatch::class)->toBe($this->response)
        ->getBatch()->toBeInstanceof(Batch::class)
        ->getProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveCount(2)
            ->toHaveKeys([
                ShowProduct::BATCH_PROP,
                'product',
            ])
        );
});

it('is ineertia response', function () {
    $is = 'Show';

    get(route('products.show', $this->product))
        ->assertInertia(fn ($page) => $page
            ->component($is)
            ->where('title', $is)
            ->where('head', $is)
            ->has('product')
        );
});
