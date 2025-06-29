<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\CreateResponse;
use Honed\Honed\Responses\InertiaResponse;

class CreateProduct extends CreateResponse
{
    protected function definition(InertiaResponse $response): InertiaResponse
    {
        return $response;
    }
}
