<?php

declare(strict_types=1);

namespace Honed\Honed\Data;

use Spatie\LaravelData\Data;

class InlineData extends Data
{
    public function __construct(
        public int|string $id,
    ) {}
}
