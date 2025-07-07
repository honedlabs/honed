<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\ShowResponse;

/**
 * @extends ShowResponse<\Workbench\App\Models\Product>
 */
class ShowProduct extends ShowResponse
{
    /**
     * Define the response.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this;
    }
}
