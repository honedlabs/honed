<?php

declare(strict_types=1);

namespace Honed\Honed\Data\Casts;

use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class DateTimeCast extends DateTimeInterfaceCast
{
    public function __construct(
        ?string $type = null,
        ?string $setTimeZone = null,
        ?string $timezone = null,
    ) {
        parent::__construct('Y-m-d\TH:i:s', $type, $setTimeZone, $timezone);
    }
}
