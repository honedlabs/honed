<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\EditResponse;
use Honed\Honed\Responses\InertiaResponse;

/**
 * @extends EditResponse<\Workbench\App\Models\Product>
 */
class EditProduct extends EditResponse
{
    protected function definition(InertiaResponse $response): InertiaResponse
    {
        return $response;
    }
}
