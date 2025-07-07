<?php

declare(strict_types=1);

use Workbench\App\Http\Responses\IndexProduct;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->response = new IndexProduct();
});

it('has no table by default', function () {
    expect($this->response)
        ->getTable()->toBeNull();
});

it('has table from class string', function () {
    expect($this->response)
        ->table(ProductTable::class)->toBe($this->response)
        ->getTable()->toBeInstanceOf(ProductTable::class);
});

it('has table from instance', function () {
    expect($this->response)
        ->table(ProductTable::make())->toBe($this->response)
        ->getTable()->toBeInstanceOf(ProductTable::class);
});

it('does not have table from model', function () {
    expect($this->response)
        ->table()->toBe($this->response)
        ->getTable()->toBeNull();
});

it('has table props', function () {
    expect($this->response)
        ->canHaveTableToProps()->toBe([])
        ->table(ProductTable::make())->toBe($this->response)
        ->canHaveTableToProps()
        ->scoped(fn ($table) => $table
            ->toBeArray()
            ->toHaveCount(1)
            ->toHaveKey(IndexProduct::TABLE_PROP)
            ->{IndexProduct::TABLE_PROP}->toBeArray()
        );
});
