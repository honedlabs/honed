<?php

declare(strict_types=1);

namespace Workbench\App\Data;

use Carbon\Carbon;
use Honed\Honed\Data\Casts\DateCast;
use Honed\Honed\Data\Casts\DateTimeCast;
use Honed\Honed\Data\Transformers\DateTimeTransformer;
use Honed\Honed\Data\Transformers\DateTransformer;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;

class DateData extends Data
{
    #[WithCast(DateTimeCast::class)]
    #[WithTransformer(DateTimeTransformer::class)]
    public ?Carbon $created_at;

    #[WithCast(DateCast::class)]
    #[WithTransformer(DateTransformer::class)]
    public ?Carbon $updated_at;
}