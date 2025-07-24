<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

use Honed\Honed\Concerns\Resourceful;

enum Status: string
{
    use Resourceful;

    case Available = 'available';
    case Unavailable = 'unavailable';
    case ComingSoon = 'coming-soon';
}
