<?php

declare(strict_types=1);

use Honed\Honed\Attributes\Internal;
use Workbench\App\Classes\Attributed;

it('creates attribute', function () {
    $attribute = new Internal();
    expect($attribute)
        ->toBeInstanceOf(Internal::class);
});

it('applies attribute', function () {
    expect(Attributed::class)
        ->toHaveAttribute(Internal::class);
});
