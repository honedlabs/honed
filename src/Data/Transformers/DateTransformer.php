<?php

namespace Honed\Honed\Data\Transformers;

use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class DateTransformer extends DateTimeInterfaceTransformer
{
    public function __construct(?string $setTimeZone = null)
    {
        return parent::__construct('Y-m-d', $setTimeZone);
    }
}
