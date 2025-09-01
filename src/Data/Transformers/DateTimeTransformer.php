<?php

declare(strict_types=1);

namespace Honed\Honed\Data\Transformers;

use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class DateTimeTransformer extends DateTimeInterfaceTransformer
{
    public function __construct(?string $setTimeZone = null)
    {
        parent::__construct('Y-m-d\TH:i:s', $setTimeZone);
    }
}
