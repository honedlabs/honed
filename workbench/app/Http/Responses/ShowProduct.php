<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\InertiaResponse;
use Honed\Honed\Responses\ShowResponse;

/**
 * @extends ShowResponse<\Workbench\App\Models\Product>
 */
class ShowProduct extends ShowResponse
{
    protected function definition(InertiaResponse $response): InertiaResponse
    {
        return $response;
    }
}
