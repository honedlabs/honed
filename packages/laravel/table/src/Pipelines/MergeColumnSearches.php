<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Closure;
use Honed\Refine\Search;
use Honed\Table\Table;
use Honed\Table\Columns\Column;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class MergeColumnSearches
{
    /**
     * Apply the sorts refining logic.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke(Table $table, Closure $next): Table
    {
        $columns = $table->getCachedColumns();

        /** @var array<int,\Honed\Refine\Search<TModel, TBuilder>> */
        $searches = \array_map(
            static fn (Column $column) => 
                Search::make($column->getName(), $column->getLabel())
                    ->alias($column->getParameter()),
            \array_values(
                \array_filter(
                    $columns,
                    static fn (Column $column) => $column->isSearchable()
                )
            )
        );

        $table->withSearches($searches);

        return $next($table);
    }
}
