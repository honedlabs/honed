<?php

declare(strict_types=1);

namespace Workbench\App\Data;

use Honed\Honed\Concerns\Hydratable;
use Spatie\LaravelData\Data;
use Workbench\App\Models\Product;

class ProductData extends Data
{
    use Hydratable;

    public function __construct(
        public int $id,
        public string $name
    ) {}

    /**
     * {@inheritDoc}
     */
    public function hydrateFrom(): string
    {
        return Product::class;
    }
}
