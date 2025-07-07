<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Responses\Concerns\CanHaveTable;

class IndexResponse extends InertiaResponse
{
    use CanHaveTable;

    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        $this->page('Index');
    }
}
