<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Action\Batch;

/**
 * @template TBatch of \Honed\Action\Batch = \Honed\Action\Batch
 */
trait CanHaveBatch
{
    public const BATCH_PROP = 'batch';

    /**
     * The batch to use for actions.
     *
     * @var class-string<TBatch>|TBatch|null
     */
    protected $batch;

    /**
     * Set the batch to use for actions.
     *
     * @param  class-string<TBatch>|TBatch|null  $value
     * @return $this
     */
    public function batch($value)
    {
        $this->batch = $value;

        return $this;
    }

    /**
     * Get the batch to use for actions.
     *
     * @return TBatch|null
     */
    public function getBatch()
    {
        return match (true) {
            is_string($this->batch) => ($this->batch)::make(),
            $this->batch instanceof Batch => $this->batch,
            default => null,
        };
    }

    /**
     * Convert the batch an array of props.
     *
     * @return array<string, mixed>
     */
    protected function batchToArray()
    {
        if ($batch = $this->getBatch()) {
            return [self::BATCH_PROP => $batch];
        }

        return [];
    }
}
