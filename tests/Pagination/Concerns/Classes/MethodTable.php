<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Pagination\Concerns\Classes;

use Honed\Table\Table;

final class MethodTable extends Table
{
    public function perPage(): int
    {
        return 20;
    }

    public function showKey(): string
    {
        return 'count';
    }

    public function pageName(): string
    {
        return 'p';
    }

    public function defaultPerPage(): int
    {
        return 20;
    }

    public function paginator()
    {
        return 'cursor';

    }
}
