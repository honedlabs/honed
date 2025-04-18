<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Handler;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasParameterNames;
use Honed\Refine\Pipelines\AfterRefining;
use Honed\Refine\Pipelines\BeforeRefining;
use Honed\Refine\Refine;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\HasColumns;
use Honed\Table\Concerns\HasPagination;
use Honed\Table\Concerns\HasTableBindings;
use Honed\Table\Concerns\IsSelectable;
use Honed\Table\Concerns\IsToggleable;
use Honed\Table\Pipelines\CleanupTable;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Pipelines\QueryColumns;
use Honed\Table\Pipelines\RefineFilters;
use Honed\Table\Pipelines\RefineSearches;
use Honed\Table\Pipelines\RefineSorts;
use Honed\Table\Pipelines\SelectColumns;
use Honed\Table\Pipelines\ToggleColumns;
use Honed\Table\Pipelines\TransformRecords;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Refine<TModel, TBuilder>
 */
class Table extends Refine implements UrlRoutable
{
    use HasActions;

    /** @use HasColumns<TModel, TBuilder> */
    use HasColumns;

    use HasEncoder;

    use HasEndpoint;

    use HasMeta;

    /** @use HasPagination<TModel, TBuilder> */
    use HasPagination {
        getPageKey as protected getBasePageKey;
        getRecordKey as protected getBaseRecordKey;
    }

    /** @use HasParameterNames<TModel, TBuilder> */
    use HasParameterNames;

    // use HasTableBindings;

    /** @use IsSelectable<TModel, TBuilder> */
    use IsSelectable;
    /** @use IsToggleable<TModel, TBuilder> */
    use IsToggleable {
        getColumnKey as protected getBaseColumnKey;
    }

    /**
     * The unique identifier column for the table.
     *
     * @var string|null
     */
    protected $key;

    /**
     * Whether the model should be serialized per record.
     *
     * @var bool|null
     */
    protected $serialize;

    /**
     * The table records.
     *
     * @var array<int,mixed>
     */
    protected $records = [];

    /**
     * The pagination data of the table.
     *
     * @var array<string,mixed>
     */
    protected $paginationData = [];

    /**
     * Create a new table instance.
     *
     * @param  \Closure(TBuilder):void|null  $before
     * @return static
     */
    public static function make($before = null)
    {
        return resolve(static::class)
            ->before($before);
    }

    /**
     * Set the record key to use.
     *
     * @param  string  $key
     * @return $this
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the unique identifier key for table records.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getKey()
    {
        if (isset($this->key)) {
            return $this->key;
        }

        $keyColumn = Arr::first(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isKey()
        );

        if ($keyColumn) {
            return $keyColumn->getName();
        }

        throw new \RuntimeException(
            'The table must have a key column or a key property defined.'
        );
    }

    /**
     * Get the endpoint to be used for table actions from the config.
     *
     * @return string
     */
    public static function getDefaultEndpoint()
    {
        return type(config('table.endpoint', '/actions'))->asString();
    }

    /**
     * Set whether the model attributes should serialized alongside columns.
     *
     * @param  bool|null  $serialize
     * @return $this
     */
    public function serialize($serialize = true)
    {
        $this->serialize = $serialize;

        return $this;
    }

    /**
     * Get whether the model should be serialized per record.
     *
     * @return bool
     */
    public function isSerialized()
    {
        if (isset($this->serialize)) {
            return $this->serialize;
        }

        return static::isSerializedByDefault();
    }

    /**
     * Get whether the model should be serialized per record from the config.
     *
     * @return bool
     */
    public static function isSerializedByDefault()
    {
        return (bool) config('table.serialize', false);
    }

    /**
     * Set the records for the table.
     *
     * @param  array<int,mixed>  $records
     * @return void
     */
    public function setRecords($records)
    {
        $this->records = $records;
    }

    /**
     * Get the records from the table.
     *
     * @return array<int,mixed>
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set the pagination data for the table.
     *
     * @param  array<string,mixed>  $paginationData
     * @return void
     */
    public function setPaginationData($paginationData)
    {
        $this->paginationData = $paginationData;
    }

    /**
     * Get the pagination data from the table.
     *
     * @return array<string,mixed>
     */
    public function getPaginationData()
    {
        return $this->paginationData;
    }

    /**
     * Get the query parameter for the page number.
     *
     * @return string
     */
    public function getPageKey()
    {
        return $this->formatScope($this->getBasePageKey());
    }

    /**
     * Get the query parameter for the number of records to show per page.
     *
     * @return string
     */
    public function getRecordKey()
    {
        return $this->formatScope($this->getBaseRecordKey());
    }

    /**
     * Get the query parameter for which columns to display.
     *
     * @return string
     */
    public function getColumnKey()
    {
        return $this->formatScope($this->getBaseColumnKey());
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultDelimiter()
    {
        return type(config('table.delimiter', ','))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultSearchKey()
    {
        return type(config('table.search_key', 'search'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultMatchKey()
    {
        return type(config('table.match_key', 'match'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function isMatchingByDefault()
    {
        return (bool) config('table.match', false);
    }

    /**
     * Handle the incoming action request for this table.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function handle($request)
    {
        return Handler::make(
            $this->getBuilder(),
            $this->getActions(),
            $this->getKey()
        )->handle($request);
    }

    /**
     * Build the table. Alias for `refine`.
     *
     * @return $this
     */
    public function build()
    {
        return $this->refine();
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $value
     * @return static|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        /** @var static|null */
        return $this->getPrimitive($value, Table::class);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $value
     * @return static|null
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        return $this->resolveRouteBinding($value, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return 'table';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKey()
    {
        return static::encode(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $this->build();

        $id = $this->isWithoutActions() ? null : $this->getRouteKey();

        return \array_merge(parent::toArray(), [
            'id' => $id,
            'records' => $this->getRecords(),
            'paginator' => $this->getPaginationData(),
            'columns' => $this->columnsToArray(),
            'recordsPerPage' => $this->recordsPerPageToArray(),
            'toggleable' => $this->isToggleable(),
            'actions' => $this->actionsToArray(),
            'meta' => $this->getMeta(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configToArray()
    {
        return \array_merge(parent::configToArray(), [
            'endpoint' => $this->getEndpoint(),
            'key' => $this->getKey(),
            'record' => $this->getRecordKey(),
            'column' => $this->getColumnKey(),
            'page' => $this->getPageKey(),
        ]);
    }

    /**
     * Get the actions for the table as an array.
     *
     * @return array<string, mixed>
     */
    public function actionsToArray()
    {
        return [
            'inline' => filled($this->getInlineActions()),
            'bulk' => $this->getBulkActions(),
            'page' => $this->getPageActions(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function pipeline()
    {
        App::make(Pipeline::class)
            ->send($this)
            ->through([
                BeforeRefining::class,
                ToggleColumns::class,
                RefineSearches::class,
                RefineFilters::class,
                RefineSorts::class,
                SelectColumns::class,
                QueryColumns::class,
                AfterRefining::class,
                Paginate::class,
                TransformRecords::class,
                CleanupTable::class,
            ])->thenReturn();
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        return $this->macroCall($method, $parameters);
    }
}
