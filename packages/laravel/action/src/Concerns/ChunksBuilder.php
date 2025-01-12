<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Support\Collection;
use Honed\Action\Contracts\ShouldChunk;
use Illuminate\Database\Eloquent\Builder;

trait ChunksBuilder
{
    /**
     * @var bool|null
     */
    protected $chunk;

    /**
     * @var int|null
     */
    protected $chunkSize;

    /**
     * @var bool|null
     */
    protected $chunkById;

    /**
     * Set the action to chunk the records.
     * 
     * @return $this
     */
    public function chunk(int $size = 1000, bool $chunkById = true): static
    {
        $this->chunk = true;
        $this->chunkSize = $size;
        $this->chunkById = $chunkById;

        return $this;
    }

    /**
     * Determine if the action should chunk the records.
     */
    public function chunks(): bool
    {
        return $this instanceof ShouldChunk || (bool) $this->chunk;
    }

    /**
     * Get the chunk size.
     */
    public function chunkSize(): int
    {
        return $this->chunkSize ?? 1000;
    }

    /**
     * Determine if the action should chunk by id.
     */
    public function chunkById(): bool
    {
        return $this->chunkById ?? true;
    }

    /**
     * Chunk the records using the builder.
     */
    public function chunkRecords(Builder $builder, callable $callback, bool $model = false): bool
    {
        if (! $this->chunks()) {
            return false;
        }

        return $this->chunkById() 
            ? $builder->chunkById($this->getChunkSize(), $this->provideChunkCallback($callback, $model))
            : $builder->chunk($this->getChunkSize(), $this->provideChunkCallback($callback, $model));
    }

    /**
     * Provide the chunk callback.
     */
    private function provideChunkCallback(callable $callback, bool $model): \Closure
    {
        return $model 
            ? function (Collection $records) use ($callback) {
                foreach ($records as $record) {
                    \call_user_func($callback, $record);
                }
            }
            : function (Collection $records) use ($callback) {
                \call_user_func($callback, $records);
            };
    }
}

