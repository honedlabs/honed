<?php

declare(strict_types=1);

namespace Honed\Honed\Data\Casts;

use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class DateCast extends DateTimeInterfaceCast
{
    public function __construct(
        ?string $type = null,
        ?string $setTimeZone = null,
        ?string $timezone = null,
    ) {
        parent::__construct('Y-m-d', $type, $setTimeZone, $timezone);
    }
}
