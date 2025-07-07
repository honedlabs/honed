<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Responses\Concerns\HasStore;

class CreateResponse extends InertiaResponse
{
    use HasStore;

    /**
     * Create a new edit response.
     */
    public function __construct(string $store)
    {
        $this->store($store);
    }
}
