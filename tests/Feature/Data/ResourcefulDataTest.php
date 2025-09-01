<?php

declare(strict_types=1);

namespace Tests\Feature\Data;

use Honed\Honed\Data\ResourcefulData;
use Workbench\App\Enums\Type;

beforeEach(function () {});

it('creates a data object from a resourceful', function () {
    expect(ResourcefulData::from(Type::Product))
        ->toBeInstanceOf(ResourcefulData::class)
        ->label->toBe(Type::Product->label())
        ->value->toBe(Type::Product->value());
});
