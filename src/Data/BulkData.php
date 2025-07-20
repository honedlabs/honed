<?php

declare(strict_types=1);

namespace Honed\Honed\Data;

use Spatie\LaravelData\Attributes\Validation\ListType;
use Spatie\LaravelData\Data;

class BulkData extends Data
{
    public function __construct(
        public bool $all,
        #[ListType]
        public array $only,
        #[ListType]
        public array $except,
    ) {}
}
