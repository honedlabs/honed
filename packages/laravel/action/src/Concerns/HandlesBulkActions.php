<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Honed\Action\Contracts\ShouldChunk;
use Honed\Action\Contracts\ShouldChunkById;
use Honed\Core\Concerns\HasQuery;
use Honed\Core\Parameters;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Collection;
use ReflectionFunction;
use ReflectionNamedType;
use RuntimeException;

use function array_merge;

trait HandlesBulkActions
{
    /** @use HasQuery<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>> */
    use HasQuery;

    /**
     * Whether the action should be chunked.
     *
     * @var bool|null
     */
    protected $chunk;

    /**
     * Whether actions should be chunked by default.
     *
     * @var bool
     */
    protected static $shouldChunk = false;

    /**
     * Whether the bulk action should chunk the records by id.
     *
     * @var bool|null
     */
    protected $chunkById;

    /**
     * Whether actions should be chunked by id by default.
     *
     * @var bool
     */
    protected static $shouldChunkById = true;

    /**
     * The size of the chunk to use when chunking the records.
     *
     * @var int|null
     */
    protected $chunkSize;

    /**
     * The default size of the chunk to use when chunking the records.
     *
     * @var int
     */
    protected static $useChunkSize = 500;

    /**
     * Throw an exception for a chunked handler.
     *
     * @return never
     *
     * @throws RuntimeException
     */
    public static function throwChunkedHandlerException()
    {
        throw new RuntimeException(
            'A chunked handler cannot reference the builder.'
        );
    }

    /**
     * Set the action to chunk the records.
     *
     * @param  bool|null  $chunks
     * @return $this
     */
    public function chunk($chunk = true)
    {
        $this->chunk = $chunk;

        return $this;
    }

    /**
     * Determine if the action should use chunking.
     *
     * @return bool
     */
    public function chunks()
    {
        if ($this instanceof ShouldChunk) {
            return true;
        }

        return $this->chunk ?? static::$shouldChunk;
    }

    /**
     * Set whether the action should use chunking by default.
     * 
     * @param  bool  $chunk
     * @return void
     */
    public static function shouldChunk($chunk = true)
    {
        static::$shouldChunk = $chunk;
    }

    /**
     * Set the action to chunk the records by id.
     *
     * @param  bool|null  $chunksById
     * @return $this
     */
    public function chunkById($byId = true)
    {
        $this->chunkById = $byId;

        return $this->chunk();
    }

    /**
     * Determine if the action should chunk the records by id.
     *
     * @return bool
     */
    public function isChunkedById()
    {
        if ($this instanceof ShouldChunkById) {
            return true;
        }

        return $this->chunkById ?? static::$shouldChunkById;
    }

    /**
     * Set whether the action should chunk the records by id by default.
     *
     * @param  bool  $byId
     * @return void
     */
    public static function shouldChunkById($byId = true)
    {
        static::$shouldChunkById = $byId;
    }

    /**
     * Set the size of the chunk to use when chunking the records.
     *
     * @param  int|null  $size
     * @return $this
     */
    public function chunkSize($size)
    {
        $this->chunkSize = $size;

        return $this;
    }

    /**
     * Get the size of the chunk to use when chunking the records.
     *
     * @return int
     */
    public function getChunkSize()
    {
        return $this->chunkSize ?? static::$useChunkSize;
    }

    /**
     * Set the default size of the chunk to use when chunking the records.
     *
     * @param  int  $size
     * @return void
     */
    public static function useChunkSize($size)
    {
        static::$useChunkSize = $size;
    }

    /**
     * Execute the bulk action on a builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function execute($builder)
    {
        $handler = $this->getHandler();

        if (! $handler) {
            return null;
        }

        $model = $builder->getModel()::class;
        $type = $this->getHandlerType($handler, $model);

        if ($type === 'builder' && $this->isChunked()) {
            static::throwChunkedHandlerException();
        }

        $this->modifyQuery($builder);

        $handler = $type === 'model'
            ? fn ($records) => $records->each($handler)
            : $handler;

        if ($this->isChunked()) {
            $handler = $this->wrapHandlerForChunking($handler);
        } elseif ($type !== 'builder') {
            $builder = $builder->get();
        }

        [$named, $typed] = $this->getEvaluationParameters($model, $builder);

        return $this->evaluate($handler, $named, $typed);
    }

    /**
     * Determine if the handler type references the builder, collection, or model.
     *
     * @param  Closure  $handler
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return 'builder'|'collection'|'model'|null
     */
    public function getHandlerType($handler, $model)
    {
        $parameters = (new ReflectionFunction($handler))->getParameters();

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();

            $typed = $parameter->getType();

            $type = $typed instanceof ReflectionNamedType
                ? $typed->getName()
                : null;

            if (Parameters::isBuilder($name, $type)) {
                return 'builder';
            }

            if (Parameters::isCollection($name, $type)) {
                return 'collection';
            }

            if (Parameters::isModel($name, $type, $model)) {
                return 'model';
            }
        }

        return null;
    }

    /**
     * Get the named and typed parameters to use for the bulk action.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|DatabaseCollection<int,\Illuminate\Database\Eloquent\Model>  $value
     * @return array{array<string, mixed>,  array<class-string, mixed>}
     */
    public function getEvaluationParameters($model, $value)
    {
        [$named, $typed] = Parameters::builder($model, $value);

        $named = array_merge($named, [
            'collection' => $value,
        ]);

        $typed = array_merge($typed, [
            DatabaseCollection::class => $value,
            Collection::class => $value,
        ]);

        return [$named, $typed];
    }

    /**
     * Wrap the handler for chunking.
     *
     * @param  Closure  $handler
     * @return Closure
     */
    protected function wrapHandlerForChunking($handler)
    {
        $chunkSize = $this->getChunkSize();

        return $this->isChunkedById()
            ? fn ($builder) => $builder->chunkById($chunkSize, $handler)
            : fn ($builder) => $builder->chunk($chunkSize, $handler);
    }
}
