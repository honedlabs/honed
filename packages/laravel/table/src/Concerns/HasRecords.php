<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait HasRecords
{
    /**
     * The records of the table retrieved from the resource.
     * 
     * @var \Illuminate\Support\Collection<array-key,array<array-key,mixed>>|null
     */
    protected $records = null;

        /**
     * The number of records to show per page. 
     * An array provides options allowing users to change the number of records shown to themper page.
     * 
     * @var int|array<int,int>
     */
    protected $perPage;

    /**
     * The default number of records to show per page.
     * If $perPage is an array, this should be one of the values.
     * If not supplied, the lowest value in $perPage will be used.
     * 
     * @var int
     */
    protected $defaultPerPage;

    /**
     * The number of records to use per page for all tables.
     * 
     * @var int|array<int,int>
     */
    protected static $defaultPerPageAmount = 10;

    /**
     * The paginator instance to use for the table.
     * 
     * @var class-string|null
     */
    protected $paginator;

    /**
     * The paginator type to use for all tables.
     * 
     * @var class-string
     */
    protected static $defaultPaginator = LengthAwarePaginator::class;

    /**
     * The name to use for the page query parameter.
     * @var string
     */
    protected $page;

    /**
     * The name to use for the page query parameter for all tables.
     * 
     * @var string|null
     */
    protected static $pageKey = null;

        /**
     * Get the records of the table.
     *
     * @return \Illuminate\Support\Collection<int,array<string,mixed>>|null
     */
    public function getRecords(): ?Collection
    {
        return $this->records;
    }

    /**
     * Determine if the table has records.
     */
    public function hasRecords(): bool
    {
        return ! \is_null($this->records);
    }

    /**
     * Set the records of the table.
     * 
     * @param  \Illuminate\Support\Collection<int,array<string,mixed>>  $records
     */
    public function setRecords(Collection $records): void
    {
        $this->records = $records;
    } 
    
    /**
     * Configure the options for the number of items to show per page.
     *
     * @param  int|array<int,int>  $perPage
     * @return void
     */
    public static function usePerPage(int|array $perPage)
    {
        static::$usePerPage = $perPage;
    }

    /**
     * Configure the default paginator to use.
     *
     * @param  string|\Honed\Table\Enums\Paginator  $paginator
     * @return void
     */
    public static function usePaginator(string|Paginator $paginator)
    {
        static::$usePaginatorType = $paginator;
    }

    /**
     * Configure the query parameter to use for the page number.
     *
     * @return void
     */
    public static function pageName(string $name)
    {
        static::$pageName = $name;
    }

    /**
     * Configure the query parameter to use for the number of items to show.
     *
     * @return void
     */
    public static function showName(string $name)
    {
        static::$showName = $name;
    }

    /**
     * Get the options for the number of items to show per page.
     *
     * @return int|array<int,int>
     */
    public function getPerPage()
    {
        return $this->inspect('perPage', static::$usePerPage);
    }

    /**
     * Get the default paginator to use.
     *
     * @return string|\Honed\Table\Enums\Paginator
     */
    public function getPaginatorType()
    {
        return $this->inspect('paginatorType', static::$usePaginatorType);
    }

    /**
     * Get the query parameter to use for the page number.
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->inspect('page', static::$pageName);
    }

    /**
     * Get the query parameter to use for the number of items to show.
     *
     * @return string
     */
    public function getShowName()
    {
        return $this->inspect('show', static::$showName);
    }

    /**
     * Get the pagination options for the number of items to show per page.
     *
     * @return array<int, array{value: int, active: bool}>
     */
    public function getPaginationCounts(?int $active = null): array
    {
        $perPage = $this->getRecordsPerPage();

        return is_array($perPage)
            ? array_map(fn ($count) => ['value' => $count, 'active' => $count === $active], $perPage)
            : [['value' => $perPage, 'active' => true]];
    }

    public function getRecordsPerPage(): int|false
    {
        $request = request();

        if ($this->getPaginatorType() === 'none') {
            return false;
        }

        // Only an array can have pagination options, so short circuit if not an array
        if (! \is_array($this->getPerPage())) {
            return $this->getPerPage();
        }

        // Force integer
        $fromRequest = $request->integer($this->getPerPageName());

        // Loop over the options to create a serializable array

        // Must ensure the query param is in the array to prevent abuse of 1000s of records

        // 0 indicates no term is provided, so use the first option
        if ($fromRequest === 0) {
            return $this->getPerPage()[0];
        }

        return $this->getPerPage();
    }

    /**
     * Execute the query and paginate the results.
     */
    public function paginateRecords(Builder $query): Paginator|CursorPaginator|Collection
    {
        $paginator = match ($this->getPaginatorType()) {
            LengthAwarePaginator::class => $query->paginate(
                perPage: $this->getRecordsPerPage(),
                pageName: $this->getPageName(),
            ),
            Paginator::class => $query->simplePaginate(
                perPage: $this->getRecordsPerPage(),
                pageName: $this->getPageName(),
            ),
            CursorPaginator::class => $query->cursorPaginate(
                perPage: $this->getRecordsPerPage(),
                cursorName: $this->getPageName(),
            ),
            'none' => $query->get(),
            default => throw new \Exception("Invalid paginator type provided [{$this->getPaginatorType()}]"),
        };

        return $paginator->withQueryString();
    }
}
