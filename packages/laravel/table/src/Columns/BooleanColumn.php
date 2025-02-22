<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BooleanColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();

        $this->type('boolean');
        $this->formatBoolean();
    }
}
