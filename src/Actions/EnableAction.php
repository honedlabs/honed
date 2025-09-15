<?php

declare(strict_types=1);

namespace Honed\Honed\Actions;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model&\Honed\Disable\Contracts\Disableable
 *
 * @extends \Honed\Honed\Actions\DisableAction<TModel>
 */
class EnableAction extends DisableAction
{
    /**
     * The disable flag.
     *
     * @var bool
     */
    public const DISABLE = false;
}
