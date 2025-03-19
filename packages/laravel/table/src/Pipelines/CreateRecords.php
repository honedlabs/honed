<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Closure;
use Honed\Table\Table;

class CreateRecords
{
    /**
     * Apply the filters to the query.
     * 
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
     * 
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke(Table $table, Closure $next): Table
    {
        $actions = $table->getInlineActions();
        $columns = $table->getColumns();

        $table->setRecords(
            \array_map(
                static fn ($record) => static::createRecord($record, $columns, $actions),
                $table->getRecords()
            )
        );

        return $next($table);
    }
}
