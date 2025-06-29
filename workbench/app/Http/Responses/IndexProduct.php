<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\IndexResponse;
use Honed\Honed\Responses\InertiaResponse;
use Workbench\App\Tables\ProductTable;

/**
 * @extends IndexResponse<ProductTable>
 */
class IndexProduct extends IndexResponse
{
    protected function definition(InertiaResponse $response): InertiaResponse
    {
        return $response;
    }
}
