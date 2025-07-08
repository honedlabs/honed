<?php

declare(strict_types=1);

use Honed\Honed\Attributes\Experimental;
use Workbench\App\Classes\Attributed;

it('creates attribute', function () {
    $attribute = new Experimental();
    expect($attribute)
        ->toBeInstanceOf(Experimental::class);
});

it('applies attribute', function () {
    expect(Attributed::class)
        ->toHaveAttribute(Experimental::class);
});