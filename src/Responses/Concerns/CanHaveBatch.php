<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Action\Batch;
use Honed\Honed\Contracts\Modelable;

trait CanHaveBatch
{
    public const BATCH_PROP = 'batch';

    /**
     * The batch to use for actions.
     *
     * @var bool|class-string<Batch>|Batch
     */
    protected $batch = false;

    /**
     * Set the batch to use for actions.
     *
     * @param  bool|class-string<Batch>|Batch  $value
     * @return $this
     */
    public function batch(bool|string|Batch $value = true): static
    {
        $this->batch = $value;

        return $this;
    }

    /**
     * Get the batch to use for actions.
     */
    public function getBatch(): ?Batch
    {
        $batch = match (true) {
            is_string($this->batch) => ($this->batch)::make(),
            $this->batch instanceof Batch => $this->batch,
            $this->batch === true && $this instanceof Modelable => $this->getModel()->batch(), // @phpstan-ignore-line method.notFound
            default => null,
        };

        if ($this instanceof Modelable && $batch) {
            $batch->record($this->getModel());
        }

        return $batch;
    }

    /**
     * Convert the batch an array of props.
     *
     * @return array<string, mixed>
     */
    public function canHaveBatchToProps(): array
    {
        if ($batch = $this->getBatch()) {
            return [
                self::BATCH_PROP => $batch->toArray(),
            ];
        }

        return [];
    }
}
