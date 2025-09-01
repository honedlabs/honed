<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

use Honed\Honed\Contracts\Resourceful;

enum Type: string implements Resourceful
{
    case Product = 'product';
    case Service = 'service';

    /**
     * {@inheritdoc}
     */
    public function label(): string
    {
        return $this->name.'s';
    }

    /**
     * {@inheritdoc}
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
