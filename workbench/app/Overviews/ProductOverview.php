<?php

declare(strict_types=1);

namespace Workbench\App\Overviews;

use Honed\Stats\Overview;

class ProductOverview extends Overview
{
    /**
     * Define the profile.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this;
    }
}
