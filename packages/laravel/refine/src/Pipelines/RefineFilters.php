<?php

declare(strict_types=1);

namespace Honed\Refine\Pipelines;

use Closure;
use Honed\Refine\Refine;

final readonly class RefineFilters
{
    /**
     * Apply the filters to the query.
     * 
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
     * 
     * @param  \Honed\Refine\Refine<TModel, TBuilder>  $refine
     * @return \Honed\Refine\Refine<TModel, TBuilder>
     */
    public function __invoke(Refine $refine, Closure $next): Refine
    {
        if (! $refine->isFiltering()) {
            return $next($refine);
        }

        $scope = $refine->getScope();
        $delimiter = $refine->getDelimiter();

        $for = $refine->getFor();
        $request = $refine->getRequest();

        $filters = $refine->getFilters();

        foreach ($filters as $filter) {
            $filter->scope($scope)
                ->delimiter($delimiter)
                ->refine($for, $request);
        }

        return $next($refine);
    }
}
