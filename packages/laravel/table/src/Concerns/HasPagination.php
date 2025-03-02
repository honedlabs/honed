<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\PerPageRecord;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template-covariant TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
trait HasPagination
{
    /**
     * The paginator to use.
     *
     * @var 'cursor'|'simple'|'length-aware'|'collection'|string|null
     */
    protected $paginator;

    /**
     * The pagination options.
     *
     * @var int|array<int,int>|null
     */
    protected $pagination;

    /**
     * The default pagination amount if pagination is an array.
     *
     * @var int|null
     */
    protected $defaultPagination;

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
     * The records per page options if dynamic.
     *
     * @var array<int,\Honed\Table\PerPageRecord>
     */
    protected $recordsPerPage = [];

    /**
     * Retrieve the default paginator for the table.
     *
     * @return 'cursor'|'simple'|'length-aware'|'collection'|string
     */
    public function getPaginator()
    {
        if (isset($this->paginator)) {
            return $this->paginator;
        }

        return $this->fallbackPaginator();
    }

    /**
     * Retrieve the default paginator for the table.
     *
     * @return 'cursor'|'simple'|'length-aware'|'collection'|string
     */
    protected function fallbackPaginator()
    {
        return type(config('table.paginator', 'length-aware'))->asString();
    }

    /**
     * Retrieve the pagination options for the table.
     *
     * @return int|array<int,int>
     */
    public function getPagination()
    {
        if (isset($this->pagination)) {
            return $this->pagination;
        }

        if (\method_exists($this, 'pagination')) {
            return $this->pagination();
        }

        return $this->getDefaultPagination();
    }

    /**
     * Retrieve the default pagination options for the table.
     * 
     * @return int
     */
    public function getDefaultPagination()
    {
        if (isset($this->default)) {
            return $this->default;
        }

        return $this->fallbackDefaultPagination();
    }

    /**
     * Get records per page options.
     *
     * @return array<int,\Honed\Table\PerPageRecord>
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * Get the records per page options as an array.
     * 
     * @return array<int,array<string,mixed>>
     */
    public function recordsPerPageToArray()
    {
        return \array_map(
            static fn (PerPageRecord $record) => $record->toArray(),
            $this->getRecordsPerPage()
        );
    }
    
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

        return $this->fallbackPagesKey();
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

        return $this->fallbackRecordsKey();
    }

    
    /**
     * Create the record per page options for the table.
     *
     * @param  array<int,int>  $pagination
     * @param  int  $active
     * @return void
     */
    public function createRecordsPerPage($pagination, $active)
    {
        $this->recordsPerPage = \array_map(
            static fn (int $amount) => PerPageRecord::make($amount, $active),
            $pagination
        );
    }

    /**
     * Get the fallback default pagination options for the table.
     *
     * @return int
     */
    protected function fallbackDefaultPagination()
    {
        return type(config('table.pagination.default', 10))->asInt();
    }

    /**
     * Get the query parameter for the page number.
     * 
     * @return string
     */
    protected function fallbackPagesKey()
    {
        return type(config('table.config.pages', 'page'))->asString();
    }

    /**
     * Get the query parameter for the number of records to show per page.
     * 
     * @return string
     */
    protected function fallbackRecordsKey()
    {
        return type(config('table.config.records', 'rows'))->asString();
    }

    /**
     * Determine if the paginator is a length-aware paginator.
     * 
     * @param string|'length-aware'|'simple'|'cursor'|'collection' $paginator
     * @return bool
     */
    protected static function isLengthAware($paginator)
    {
        return \in_array($paginator, [
            'length-aware',
            \Illuminate\Contracts\Pagination\LengthAwarePaginator::class,
            \Illuminate\Pagination\LengthAwarePaginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a simple paginator.
     * 
     * @param string|'length-aware'|'simple'|'cursor'|'collection' $paginator
     * @return bool
     */
    protected static function isSimple($paginator)
    {
        return \in_array($paginator, [
            'simple',
            \Illuminate\Contracts\Pagination\Paginator::class,
            \Illuminate\Pagination\Paginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a cursor paginator.
     * 
     * @param string|'length-aware'|'simple'|'cursor'|'collection' $paginator
     * @return bool
     */
    protected static function isCursor($paginator)
    {
        return \in_array($paginator, [
            'cursor',
            \Illuminate\Contracts\Pagination\CursorPaginator::class,
            \Illuminate\Pagination\CursorPaginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a collection.
     * 
     * @param string|'length-aware'|'simple'|'cursor'|'collection' $paginator
     * @return bool
     */
    protected static function isCollection($paginator)
    {
        return \in_array($paginator, [
            'none',
            'collection',
            \Illuminate\Support\Collection::class,
        ]);
    }

    /**
     * Get the pagination data for the length-aware paginator.
     * 
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator<TModel> $paginator
     * @return array<string, mixed>
     */
    protected static function lengthAwarePaginator($paginator)
    {
        return \array_merge(static::simplePaginator($paginator), [
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'firstLink' => $paginator->url(1),
            'lastLink' => $paginator->url($paginator->lastPage()),
            'links' => static::createPaginatorLinks($paginator),
        ]);
    }

    /**
     * Create pagination links with a sliding window around the current page.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<TModel>  $paginator
     * @return array<int, array<string, mixed>>
     */
    protected static function createPaginatorLinks($paginator)
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = 2;
        
        // Calculate window boundaries with balanced distribution
        $start = max(1, min($currentPage - $onEachSide, $lastPage - ($onEachSide * 2)));
        $end = min($lastPage, max($currentPage + $onEachSide, ($onEachSide * 2 + 1)));
        
        return collect(range($start, $end))
            ->map(static fn ($page) => [
                'url' => $paginator->url($page),
                'label' => (string) $page,
                'active' => $currentPage === $page,
            ])
            ->values()
            ->all();
    }

    /**
     * Get the pagination data for the simple paginator.
     * 
     * @param \Illuminate\Contracts\Pagination\Paginator<TModel> $paginator
     * @return array<string, mixed>
     */
    protected static function simplePaginator($paginator)
    {
        return \array_merge(static::cursorPaginator($paginator), [
            'currentPage' => $paginator->currentPage(),
        ]);
    }

    /**
     * Get the pagination data for the cursor paginator.
     * 
     * @param \Illuminate\Pagination\AbstractCursorPaginator<TModel>|\Illuminate\Contracts\Pagination\Paginator<TModel> $paginator
     * @return array<string, mixed>
     */
    protected static function cursorPaginator($paginator)
    {
        return \array_merge(static::collectionPaginator($paginator), [
            'prevLink' => $paginator->previousPageUrl(),
            'nextLink' => $paginator->nextPageUrl(),
            'perPage' => $paginator->perPage(),
        ]);
    }

    /**
     * Get the base metadata for the collection paginator, and all others.
     * 
     * @param \Illuminate\Support\Collection<int,TModel>|\Illuminate\Pagination\AbstractCursorPaginator<TModel>|\Illuminate\Contracts\Pagination\Paginator<TModel> $paginator
     * @return array<string, mixed>
     */
    protected static function collectionPaginator($paginator)
    {
        return [
            'empty' => $paginator->isEmpty(),
        ];
    }

    /**
     * Ensure that the pagination count is a valid option.
     * 
     * @param  int  $count
     * @param  array<int,int>  $options
     * @return void
     */
    protected function validatePagination(&$count, $options)
    {
        if (\in_array($count, $options)) {
            return;
        }

        $count = $this->getDefaultPagination();
    }

    /**
     * Paginate the data.
     * 
     * @param TBuilder $builder
     * @param int $count
     * @return array{0: \Illuminate\Support\Collection<int,TModel>, 1: array<string,mixed>}
     * @throws \InvalidArgumentException
     */
    protected function paginate($builder, $count)
    {
        $paginator = $this->getPaginator();
        $key = $this->getPagesKey();

        [$data, $method] = match (true) {
            static::isLengthAware($paginator) => [
                $builder->paginate($count, pageName: $key),
                'lengthAwarePaginator',
            ],
            static::isSimple($paginator) => [
                $builder->simplePaginate($count, pageName: $key),
                'simplePaginator',
            ],
            static::isCursor($paginator) => [
                $builder->cursorPaginate(perPage: $count, cursorName: $key),
                'cursorPaginator',
            ],
            static::isCollection($paginator) => [
                $builder->get(),
                'collectionPaginator',
            ],
            default => static::throwInvalidPaginatorException($paginator),
        };

        if (! $data instanceof Collection) {
            $data->withQueryString();
        }

        $paginationData = \call_user_func([static::class, $method], $data);
        return [
            $data instanceof Collection ? $data : $data->getCollection(), 
            $paginationData,
        ];
    }

    /**
     * Throw an exception if the paginator is invalid.
     * 
     * @param string|'cursor'|'simple'|'length-aware'|'collection' $paginator
     * @return never
     */
    protected static function throwInvalidPaginatorException($paginator)
    {
        throw new \InvalidArgumentException(
            \sprintf('The provided paginator [%s] is invalid.', $paginator)
        );
    }
}
