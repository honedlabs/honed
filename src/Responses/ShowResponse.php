<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Contracts\ViewsModel;
use Honed\Honed\Responses\Concerns\CanHaveBatch;
use Honed\Honed\Responses\Concerns\CanHaveInfolist;
use Honed\Honed\Responses\Concerns\CanHaveStats;
use Honed\Honed\Responses\Concerns\CanHaveTable;
use Honed\Honed\Responses\Concerns\HasModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @implements ViewsModel<TModel>
 */
abstract class ShowResponse extends InertiaResponse implements ViewsModel
{
    use CanHaveBatch;
    use CanHaveInfolist;
    use CanHaveStats;
    use CanHaveTable;

    /** @use HasModel<TModel> */
    use HasModel;

    /**
     * Create a new show response.
     *
     * @param  TModel  $model
     */
    public function __construct(Model $model)
    {
        $this->model($model);
    }
}
