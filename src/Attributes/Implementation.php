<?php

declare(strict_types=1);

namespace Honed\Honed\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Implementation
{
    /**
     * Create a new attribute instance.
     */
    public function __construct(
        public ?string $implementationOf = null,
    ) {}
}
