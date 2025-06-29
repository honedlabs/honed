<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Responses\Concerns\CanHaveBatch;
use Honed\Honed\Responses\Concerns\HasModel;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBatch of \Honed\Action\Batch = \Honed\Action\Batch
 */
abstract class ShowResponse extends InertiaResponse
{
    /** @use CanHaveBatch<TBatch> */
    use CanHaveBatch;

    /** @use HasModel<TModel> */
    use HasModel;

    /**
     * Create a new show response.
     *
     * @param  TModel  $model
     */
    public function __construct($model)
    {
        $this->model($model);
    }

    /**
     * Get the props for the view.
     *
     * @return array<string, mixed>
     */
    public function getProps()
    {
        return [
            ...parent::getProps(),
            ...$this->batchToArray(),
            ...$this->modelToArray(),
        ];
    }
}
