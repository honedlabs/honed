<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Closure;
use Honed\Table\Pipes\Contracts\Filters;
use Honed\Table\Table;

/**
 * @internal
 */
class ApplyFilters implements Filters
{
    public function handle(Table $table, Closure $next)
    {
        $builder = $table->getResource();
        foreach ($table->getFilters() as $filter) {
            $filter->apply($builder);
        }

        $table->setResource($builder);

        return $next($table);
    }
}
