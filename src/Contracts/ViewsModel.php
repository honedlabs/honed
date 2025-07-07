<?php

declare(strict_types=1);

namespace Honed\Honed\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface ViewsModel
{
    /**
     * Get the model for the view.
     *
     * @return TModel
     */
    public function getModel(): Model;
}
