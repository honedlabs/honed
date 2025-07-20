<?php

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
