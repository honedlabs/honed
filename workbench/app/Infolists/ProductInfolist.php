<?php

declare(strict_types=1);

namespace Workbench\App\Infolists;

use Honed\Infolist\Infolist;

class ProductInfolist extends Infolist
{
    /**
     * Define the profile.
     *
     * @return $this
     */
    protected function definition(Infolist $infolist): Infolist
    {
        return $this;
    }
}
