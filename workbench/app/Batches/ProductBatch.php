<?php

declare(strict_types=1);

namespace Workbench\App\Batches;

use Honed\Action\Batch;
use Honed\Action\Operations\InlineOperation;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<\TModel>
 *
 * @extends \Honed\Action\Batch<TModel, TBuilder>
 */
class ProductBatch extends Batch
{
    /**
     * Define the operations for the batch.
     *
     * @param  $this  $batch
     * @return $this
     */
    protected function definition(Batch $batch): Batch
    {
        return $batch
            ->operations([
                InlineOperation::make('edit'),
            ]);
    }
}
