<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Contracts\Modelable;
use Honed\Honed\Responses\Concerns\HasDestroy;
use Honed\Honed\Responses\Concerns\HasModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @implements Modelable<TModel>
 */
class DeleteResponse extends InertiaResponse implements Modelable
{
    use HasDestroy;

    /** @use HasModel<TModel> */
    use HasModel;

    /**
     * Create a new edit response.
     *
     * @param  TModel  $model
     */
    public function __construct(Model $model, string $destroy)
    {
        $this->model($model);
        $this->destroy($destroy);
    }
}
