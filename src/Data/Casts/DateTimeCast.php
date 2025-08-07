<?php

declare(strict_types=1);

namespace Workbench\App\Data;

use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class DateTimeCast extends DateTimeInterfaceCast
{
    public function __construct(
        ?string $type = null,
        ?string $setTimeZone = null,
        ?string $timezone = null,
    ) {
        return parent::__construct('Y-m-d\TH:i:s', $type, $setTimeZone, $timezone);
    }
}
