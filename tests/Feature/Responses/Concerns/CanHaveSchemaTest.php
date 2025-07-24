<?php

declare(strict_types=1);

use Workbench\App\Data\ProductData;
use Workbench\App\Http\Responses\CreateProduct;

beforeEach(function () {
    $this->response = new CreateProduct('store');
});

it('can have schema', function () {
    expect($this->response)
        ->getSchema()->toBeNull()
        ->canHaveSchemaToProps()->toBe([])
        ->schema(ProductData::class)->toBe($this->response)
        ->getSchema()->toBe(ProductData::class)
        ->canHaveSchemaToProps()->toBe([
            'schema' => ProductData::empty([]),
        ]);
});

it('can have defaults', function () {
    expect($this->response)
        ->schema(ProductData::class)->toBe($this->response)
        ->getDefaults()->toBe([])
        ->defaults(['name' => 'Product'])->toBe($this->response)
        ->getDefaults()->toBe(['name' => 'Product'])
        ->canHaveSchemaToProps()->toBe([
            'schema' => ProductData::empty(['name' => 'Product']),
        ]);
});
