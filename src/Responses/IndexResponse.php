<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Responses\Concerns\CanHaveTable;

/**
 * @template TTable of \Honed\Table\Table = \Honed\Table\Table
 */
class IndexResponse extends InertiaResponse
{
    /** @use CanHaveTable<TTable> */
    use CanHaveTable;

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->page('Index');
    }

    /**
     * Get the props for the view.
     *
     * @return array<string, mixed>
     */
    public function getProps()
    {
        return [
            ...parent::getProps(),
            ...$this->tableToArray(),
        ];
    }
}
