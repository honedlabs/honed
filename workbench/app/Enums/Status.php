<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

use Honed\Honed\Concerns\IsResource;

enum Status: string
{
    use IsResource;

    case Available = 'available';
    case Unavailable = 'unavailable';
    case ComingSoon = 'coming-soon';
}
