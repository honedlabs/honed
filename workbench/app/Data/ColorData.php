<?php

declare(strict_types=1);

namespace Workbench\App\Data;

use Honed\Honed\Attributes\Validation\HexColor;
use Spatie\LaravelData\Data;

class ColorData extends Data
{
    #[HexColor]
    public string $color;
}
