<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Action\Concerns\HasParameterNames;
use Honed\Action\InlineAction;
use Honed\Table\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasRecords
{
    use HasParameterNames;
    use Support\HasPagination;
    use Support\HasPaginator;

    /**
     * The query parameter for the page number.
     *
     * @var string|null
     */
    protected $pagesKey;

    /**
     * The query parameter for the number of records to show per page.
     *
     * @var string|null
     */
    protected $recordsKey;

    /**
     * Set the query parameter for the page number.
     *
     * @param  string  $pagesKey
     * @return $this
     */
    public function pagesKey($pagesKey)
    {
        $this->pagesKey = $pagesKey;

        return $this;
    }

    /**
     * Get the query parameter for the page number.
     * 
     * @return string
     */
    public function getPagesKey()
    {
        if (isset($this->pagesKey)) {
            return $this->pagesKey;
        }

        return $this->getFallbackPagesKey();
    }

    /**
     * Get the query parameter for the page number.
     * 
     * @return string
     */
    protected function getFallbackPagesKey()
    {
        return type(config('table.config.pages', 'page'))->asString();
    }

    /**
     * Set the query parameter for the number of records to show per page.
     *
     * @param  string  $recordsKey
     * @return $this
     */
    public function recordsKey($recordsKey)
    {
        $this->recordsKey = $recordsKey;

        return $this;
    }

    /**
     * Get the query parameter for the number of records to show per page.
     *
     * @return string
     */
    public function getRecordsKey()
    {
        if (isset($this->recordsKey)) {
            return $this->recordsKey;
        }

        return $this->getFallbackRecordsKey();
    }

    /**
     * Get the query parameter for the number of records to show per page.
     * 
     * @return string
     */
    protected function getFallbackRecordsKey()
    {
        return type(config('table.config.records', 'rows'))->asString();
    }

    /**
     * Get the records of the table.
     *
     * @return array<int,mixed>|null
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Get the meta data of the table.
     *
     * @return array<string,mixed>
     */
    public function getPaginationData()
    {
        return $this->paginationData;
    }

    /**
     * Format the records using the provided columns.
     *
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @return void
     */
    public function retrieveRecords($columns)
    {

        [$records, $this->meta] = $this->retrievedRecords();

        $this->records = $this->formatRecords($records, $columns);
    }

    /**
     * Retrieve the records from the underlying builder, returning the records
     * collection and pagination metadata.
     *
     * @return array{0:\Illuminate\Support\Collection<int,\Illuminate\Database\Eloquent\Model>,1:array<string,mixed>}
     */
    protected function retrievedRecords()
    {
        $builder = $this->getBuilder();

        $paginator = $this->getPaginator();

        return match (true) {
            static::isLengthAware($paginator) => $this->lengthAwarePaginateRecords($builder),
            static::isSimple($paginator) => $this->simplePaginateRecords($builder),
            static::isCursor($paginator) => $this->cursorPaginateRecords($builder),
            static::isCollection($paginator) => $this->collectRecords($builder),
            default => static::throwInvalidPaginatorException($paginator),
        };
    }

    /**
     * Format the records using the provided columns.
     *
     * @param  \Illuminate\Support\Collection<int,\Illuminate\Database\Eloquent\Model>  $records
     * @param  array<int,\Honed\Table\Columns\Column>  $activeColumns
     * @return array<int,array<string,mixed>>
     */
    protected function formatRecords($records, $activeColumns)
    {
        return $records->map(
            fn (Model $record) => $this->formatRecord($record, $activeColumns)
        )->all();
    }

    /**
     * Format a record using the provided columns.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @return array<string,mixed>
     */
    protected function formatRecord($record, $columns)
    {
        [$named, $typed] = static::getNamedAndTypedParameters($record);

        $actions = collect($this->getInlineActions())
            ->filter(fn (InlineAction $action) => $action->isAllowed($named, $typed))
            ->map(fn (InlineAction $action) => $action->resolve($named, $typed))
            ->values()
            ->toArray();

        $formatted = collect($columns)
            ->mapWithKeys(fn (Column $column) => $this->formatColumn($column, $record))
            ->toArray();

        return \array_merge($formatted, ['actions' => $actions]);
    }

    /**
     * Format a single column's value for the record.
     *
     * @param  \Honed\Table\Columns\Column  $column
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @return array<string,mixed>
     */
    protected function formatColumn($column, $record)
    {
        /** @var string */
        $name = $column->getName();
        $key = Str::replace('.', '_', $name);

        return [$key => Arr::get($record, $name)];
    }

    /**
     * Length-aware paginate the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function lengthAwarePaginateRecords($builder)
    {
        /**
         * @var \Illuminate\Pagination\LengthAwarePaginator<T> $paginated
         */
        $paginated = $builder->paginate(
            perPage: $this->getRecordsPerPage(),
            pageName: $this->getPagesKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->getCollection(),
            $this->lengthAwarePaginatorMetadata($paginated),
        ];
    }

    /**
     * Simple paginate the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function simplePaginateRecords($builder)
    {
        /**
         * @var \Illuminate\Pagination\Paginator<T> $paginated
         */
        $paginated = $builder->simplePaginate(
            perPage: $this->getRecordsPerPage(),
            pageName: $this->getPagesKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->getCollection(),
            $this->simplePaginatorMetadata($paginated),
        ];
    }

    /**
     * Cursor paginate the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function cursorPaginateRecords($builder)
    {
        /**
         * @var \Illuminate\Pagination\CursorPaginator<T> $paginated
         */
        $paginated = $builder->cursorPaginate(
            perPage: $this->getRecordsPerPage(),
            cursorName: $this->getPagesKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->getCollection(),
            $this->cursorPaginatorMetadata($paginated),
        ];
    }

    /**
     * Collect the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function collectRecords($builder)
    {
        $retrieved = $builder->get();

        return [
            $retrieved,
            [],
        ];
    }

    /**
     * Throw an exception for an invalid paginator type.
     *
     * @param  string  $paginator
     * @return never
     */
    protected static function throwInvalidPaginatorException($paginator)
    {
        throw new \InvalidArgumentException(
            \sprintf('The paginator [%s] is not valid.', $paginator)
        );
    }
}
