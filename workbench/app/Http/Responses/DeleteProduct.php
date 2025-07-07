<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\DeleteResponse;

/**
 * @extends DeleteResponse<\Workbench\App\Models\Product>
 */
class DeleteProduct extends DeleteResponse
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
