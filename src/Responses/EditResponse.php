<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Contracts\ViewsModel;
use Honed\Honed\Responses\Concerns\HasModel;
use Honed\Honed\Responses\Concerns\HasUpdate;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @implements ViewsModel<TModel>
 */
class EditResponse extends InertiaResponse implements ViewsModel
{
    /** @use HasModel<TModel> */
    use HasModel;

    use HasUpdate;

    /**
     * Create a new edit response.
     *
     * @param  TModel  $model
     */
    public function __construct(Model $model, string $update)
    {
        $this->model($model);
        $this->update($update);
    }
}
