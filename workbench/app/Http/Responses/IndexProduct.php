<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\IndexResponse;
use Workbench\App\Tables\ProductTable;

/**
 * @extends IndexResponse<ProductTable>
 */
class IndexProduct extends IndexResponse
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
