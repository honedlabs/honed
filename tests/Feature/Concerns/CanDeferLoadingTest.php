<?php

declare(strict_types=1);

use Inertia\DeferProp;
use Inertia\LazyProp;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = ProductTable::make();
});

it('can defer loading', function () {
    expect($this->table->defer())
        ->toBeInstanceOf(DeferProp::class);
});

it('can lazy load', function () {
    expect($this->table->lazy())
        ->toBeInstanceOf(LazyProp::class);
});
