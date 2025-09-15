<?php

declare(strict_types=1);

namespace Honed\Honed\Actions;

use Honed\Action\Actions\DatabaseAction;
use Honed\Action\Contracts\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model&\Honed\Disable\Contracts\Disableable
 */
class DisableAction extends DatabaseAction
{
    /**
     * The disable flag.
     *
     * @var bool
     */
    public const DISABLE = true;

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TModel  $model
     */
    public function before(Model $model): void
    {
        //
    }

    /**
     * Set the disable flag.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function handle(Model $model): Model
    {
        return $this->transaction(
            fn () => $this->execute($model)
        );
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function execute(Model $model): Model
    {
        $this->before($model);

        $this->act($model);

        $this->after($model);

        return $model;
    }

    /**
     * Update the record.
     *
     * @param  TModel  $model
     */
    public function act(Model $model): void
    {
        $model->disable(static::DISABLE)->save();
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     */
    public function after(Model $model): void
    {
        //
    }
}
