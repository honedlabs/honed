<?php

declare(strict_types=1);

use Honed\Honed\Attributes\Implementation;
use Workbench\App\Classes\Attributed;

it('creates attribute', function () {
    $attribute = new Implementation('Foo');
    expect($attribute)
        ->toBeInstanceOf(Implementation::class)
        ->implementationOf->toBe('Foo');
});

it('applies attribute', function () {
    expect(Attributed::class)
        ->toHaveAttribute(Implementation::class);
});
