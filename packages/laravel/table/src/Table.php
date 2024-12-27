<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Exception;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\Inspectable;
use Honed\Core\Concerns\IsAnonymous;
use Honed\Core\Concerns\RequiresKey;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
use Honed\Core\Primitive;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Columns\BaseColumn;
use Honed\Table\Http\DTOs\BulkActionData;
use Honed\Table\Http\DTOs\InlineActionData;
use Honed\Table\Http\Requests\TableActionRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class Table extends Primitive
{
    use Concerns\Extractable;
    use Concerns\Filterable;
    use Concerns\FormatsAndPaginates;
    use Concerns\HasActions;
    use Concerns\HasColumns;
    use Concerns\HasEndpoint;
    use Concerns\Resourceful;
    use Concerns\Searchable;
    use Concerns\Selectable;
    use Concerns\Sortable;
    use Concerns\Toggleable;
    use Encodable;
    use Inspectable;
    use IsAnonymous;
    use RequiresKey;

    /**
     * The parent class-string of the table.
     * 
     * @var class-string<\Honed\Table\Table>
     */
    protected $anonymous = self::class;

    /**
     * Modify the resource query before it is used on a per controller basis.
     * 
     * @var \Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder)|null
     */
    protected $resourceModifier = null;

    /**
     * Build the table with the given assignments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|class-string|\Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder)  $resource
     */
    public function __construct(Model|Builder|Closure|string $resource = null)
    {
        if ($resource instanceof Closure) {
            $this->setResourceModifier($resource);
        } else {
            $this->setResource($resource);
        }
    }

    /**
     * Dynamically handle calls to the class for enabling anonymous table methods.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters)
    {
        match ($method) {
            'actions' => $this->setActions(...$parameters),
            'columns' => $this->setColumns(...$parameters),
            'filters' => $this->setFilters(...$parameters),
            'sorts' => $this->setSorts(...$parameters),
            // 'pages'
            // 'optimize'
            // 'reduce'
            default => parent::__call($method, $parameters)
        };

        return $this;
    }

    /**
     * Create a new table instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|class-string|\Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder)  $resource
     * @return static
     */
    public static function make(Model|Builder|Closure|string $resource = null) {
        return resolve(static::class, compact('resource'));
    }

    /**
     * Build the table records and metadata using the current request.
     *
     * @return $this
     */
    public function build(): static
    {
        $this->configureTableForCurrentRequest();

        return $this;
    }

    /**
     * Get the key name for the table records.
     *
     * @return string
     *
     * @throws MissingRequiredAttributeException
     */
    public function getKeyName()
    {
        try {
            return $this->getKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()?->getName() ?? throw $e;
        }
    }

    /**
     * Retrieve the resource modifier.
     */
    public function getResourceModifier(): ?Closure
    {
        return $this->resourceModifier;
    }

    /**
     * Set the resource modifier.
     * 
     * @param  \Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder)  $resourceModifier
     */
    public function setResourceModifier(Closure $resourceModifier): void
    {
        $this->resourceModifier = $resourceModifier;
    }

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
     * Get the table as an array.
     * 
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $this->configureTableForCurrentRequest();

        return [
            /* The ID of this table, used to deserialize it for actions */
            'id' => $this->encodeClass(),
            /* The column attribute used to identify a record */
            'keyName' => $this->getKeyName(),
            /* The records of the table */
            'records' => $this->getRecords(),
            /* The available column options */
            'columns' => $this->getColumns(),
            /* The available bulk action options */
            'bulkActions' => $this->getBulkActions(),
            /* The available page action options, generally page links */
            'pageActions' => $this->getPageActions(),
            /* The available filter options */
            'filters' => $this->getFilters(),
            /* The available sort options */
            'sorts' => $this->getSorts(),
            /* The pagination data for the records */
            'paginator' => $this->getPaginator(),
            /* Whether the table has toggling enabled */
            'toggleable' => $this->isToggleable(),
            /* The query parameter term for sorting */
            'sortName' => $this->getSortName(),
            /* The query parameter term for ordering */
            'orderName' => $this->getOrderName(),
            /* The query parameter term for changing the number of records per page */
            'countName' => $this->getCountName(),
            /* The query parameter term for searching */
            'searchName' => $this->getSearchName(),
            /* The query parameter term for toggling column visibility */
            'toggleName' => $this->getToggleName(),
            /* The route used to handle actions, it is required to be a 'post' route */
            'endpoint' => $this->getEndpoint(),
        ];
    }

    /**
     * Build the table records and metadata using the current request.
     *
     * @internal
     */
    protected function configureTableForCurrentRequest(): void
    {
        if ($this->hasRecords()) {
            return;
        }

        $this->configureSearchColumns();
        $this->configureToggleableColumns();
        $this->filterQuery($this->getQuery());
        $this->sortQuery($this->getQuery());
        $this->searchQuery($this->getQuery());
        // $this->selectQuery($this->getQuery());
        // $this->beforeRetrievingRecords($this->getQuery());
        // $this->formatAndPaginateRecords();
    }

    protected function configureSearchColumns(): void
    {
        $searchProperty = (array) $this->getSearch();

        $searchColumns = $this->getColumns()
            ->filter(static fn (BaseColumn $column): bool => $column->isSearchable())
            ->pluck('name')
            ->all();

        // Override the search property with the unique combination of the property and columns
        $this->setSearch(\array_unique([...$searchProperty, ...$searchColumns]));
    }

    protected function configureToggleableColumns(): void
    {
        $cols = $this->getToggledColumns(); // names

        if (empty($cols)) {
            return;
        }

        $this->getColumns()->each(function (BaseColumn $column) use ($cols) {
            if (\in_array($column->getName(), $cols)) {
                $column->setActive(true);
            } else {
                $column->setActive(false);
            }
        });
    }

    /**
     * Global handler for piping the request to the correct table action handler.
     */
    public function handleAction(TableActionRequest $request)
    {
        $result = match ($request->validated('type')) {
            'inline' => $this->executeInlineAction(InlineActionData::from($request)),
            'bulk' => $this->executeBulkAction(BulkActionData::from($request)),
            default => abort(404)
        };

        if ($result instanceof Response) {
            return $result;
        }

        return back();
    }

    /**
     * Execute a given inline action of the table.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function executeInlineAction(InlineActionData $data)
    {
        $action = $this->getInlineActions()->first(fn (InlineAction $action) => $action->getName() === $data->name);

        if (\is_null($action)) {
            throw new \Exception('Invalid action');
        }

        $record = $this->resolveModel($data->id);

        // Ensure that the user is authorized to perform the action on this model
        if (! $action->isAuthorized([
            'record' => $record,
            $this->getModelClassName() => $record,
        ], [
            (string) $this->getModelClass() => $record,
            Model::class => $record,
        ])) {
            throw new \Exception('Unauthorized');
        }

        return $this->evaluate(
            value: $action->getAction(),
            named: [
                'record' => $record,
                'model' => $record,
                $this->getModelClassName() => $record,
            ],
            typed: [
                (string) $this->getModelClass() => $record,
                // Model::class => $record,
            ],
        );
    }

    /**
     * Execute a given bulk action of the table.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function executeBulkAction(BulkActionData $data)
    {
        $action = $this->getBulkActions()->first(fn (BulkAction $action) => $action->getName() === $data->name);

        if (\is_null($action)) {
            throw new Exception('Invalid action');
        }

        if (! $action->isAuthorized()) {
            throw new Exception('Unauthorized');
        }

        $key = $this->getKey();

        $query = $this->getResource();
        $query = match (true) {
            $data->all => $query->whereNotIn($key, $data->except),
            default => $query->whereIn($key, $data->only)
        };

        $reflection = new \ReflectionFunction($action->getAction());
        $hasRecordsParameter = collect($reflection->getParameters())
            ->some(fn (\ReflectionParameter $parameter) => $parameter->getName() === 'records'
                || ($parameter->getType() instanceof \ReflectionNamedType && $parameter->getType()->getName() === Collection::class)
            );

        return $this->evaluate(
            value: $action->getAction(),
            named: [
                'query' => $query,
                ...($hasRecordsParameter ? ['records' => $query->get()] : []),
            ],
            typed: [
                Builder::class => $query,
                ...($hasRecordsParameter ? [Collection::class => $query->get()] : []),
            ],
        );
    }
}
