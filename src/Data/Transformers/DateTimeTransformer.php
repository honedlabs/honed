<?php

namespace Honed\Honed\Data\Transformers;

use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class DateTimeTransformer extends DateTimeInterfaceTransformer
{
    public function __construct(?string $setTimeZone = null)
    {
        return parent::__construct('Y-m-d\TH:i:s', $setTimeZone);
    }
}
