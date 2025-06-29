<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\DeleteResponse;
use Honed\Honed\Responses\InertiaResponse;

/**
 * @extends DeleteResponse<\Workbench\App\Models\Product>
 */
class DeleteProduct extends DeleteResponse
{
    protected function definition(InertiaResponse $response): InertiaResponse
    {
        return $response;
    }
}
