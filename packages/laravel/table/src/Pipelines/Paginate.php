<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Closure;
use Honed\Action\InlineAction;
use Honed\Core\Interpret;
use Honed\Table\Columns\Column;
use Honed\Table\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Paginate
{
    /**
     * Paginate the records.
     * 
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        $perPage = $this->perPage($table);

        $paginator = $table->getPaginator();
        $key = $table->formatScope($table->getPagesKey());
        $builder = $table->getFor();

        switch (true) {
            case $table->isLengthAware($paginator):
                $records = $builder->paginate($perPage, pageName: $key);

                $table->setPaginationData($table->lengthAwarePaginator($records));
                $table->setRecords($records->items());

                break;
            case $table->isSimple($paginator):
                $records = $builder->simplePaginate($perPage, pageName: $key);

                $table->setPaginationData($table->simplePaginator($records));
                $table->setRecords($records->items());

                break;
            case $table->isCursor($paginator):
                $records = $builder->cursorPaginate($perPage, cursorName: $key)
                    ->withQueryString();

                $table->setPaginationData($table->cursorPaginator($records));
                $table->setRecords($records->items());

                break;
            case $table->isCollector($paginator):
                $records = $builder->get();

                $table->setPaginationData($table->collectionPaginator($records));
                $table->setRecords($records->all());

                break;
            default:
                throw new \InvalidArgumentException(\sprintf(
                    'The provided paginator [%s] is invalid.',
                    $paginator
                ));
        }

        return $next($table);
    }

    /**
     * Get the per page value.
     * 
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return int
     */
    public function perPage($table)
    {
        $pagination = $table->getPagination();

        if (! \is_array($pagination)) {
            return $pagination;
        }

        $key = $table->formatScope($table->getRecordsKey());

        $perPage = Interpret::integer($table->getRequest(), $key);

        if (\is_null($perPage) || ! \in_array($perPage, $pagination)) {
            $perPage = $table->getDefaultPagination();
        }

        $table->createRecordsPerPage($pagination, $perPage);
        
        return $perPage;
    }
}
