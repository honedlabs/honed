<?php

declare(strict_types=1);

namespace Workbench\App\Data;

use Spatie\LaravelData\Data;

class IdData extends Data
{
    public function __construct(
        public int $id,
    ) {}
}
