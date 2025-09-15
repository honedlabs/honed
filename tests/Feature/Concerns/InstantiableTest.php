<?php

declare(strict_types=1);

use Honed\Honed\Concerns\Instantiable;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->class = new class()
    {
        use Instantiable;

        /**
         * {@inheritdoc}
         */
        public function instantiable(): string
        {
            return Product::class;
        }
    };
});

it('instantiates a class', function () {
    expect($this->class)
        ->instantiable()->toBe(Product::class)
        ->instance()->toBeInstanceOf(Product::class);
});

it('instantiates a class with arguments', function () {
    expect($this->class->instance(['name' => 'Product']))
        ->toBeInstanceOf(Product::class)
        ->getAttribute('name')->toBe('Product');
});
