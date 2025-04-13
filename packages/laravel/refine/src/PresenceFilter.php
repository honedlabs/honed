<?php

declare(strict_types=1);

namespace Honed\Refine;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends BooleanFilter<TModel, TBuilder>
 */
class PresenceFilter extends BooleanFilter
{
    /**
     * {@inheritdoc}
     */
    protected $presence = true;
}
