<?php

declare(strict_types=1);

namespace Workbench\App\Http\Responses;

use Honed\Honed\Responses\Concerns\HasUpload;
use Honed\Honed\Responses\CreateResponse;
use Workbench\App\Uploads\FileUpload;

class CreateProduct extends CreateResponse
{
    use HasUpload;

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
