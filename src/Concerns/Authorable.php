<?php

declare(strict_types=1);

namespace Honed\Honed\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Foundation\Auth\User
 *
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Model
 */
trait Authorable
{
    /**
     * Boot the HasAuthor trait.
     *
     * @return void
     */
    public static function bootAuthorable()
    {
        static::creating(function (self $model) {
            $touchedBy = $model->getTouchedByKey();

            $model->setCreatedBy($touchedBy);
            $model->setUpdatedBy($touchedBy);
        });

        static::updating(function (self $model) {
            $model->setUpdatedBy($model->getTouchedByKey());
        });
    }

    /**
     * Get the user that created the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TModel, $this>
     */
    public function author()
    {
        $model = $this->getAuthorModel();

        return $this->belongsTo(
            $model,
            $this->getCreatedByColumn(),
            (new $model())->getKeyName(),
        );
    }

    /**
     * Get the user that last edited the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TModel, $this>
     */
    public function editor()
    {
        $model = $this->getAuthorModel();

        return $this->belongsTo(
            $model,
            $this->getUpdatedByColumn(),
            (new $model())->getKeyName(),
        );
    }

    /**
     * Set the author of the model to be the initiating user.
     *
     * @param  int|string|null  $id
     * @return void
     */
    public function setCreatedBy($id)
    {
        $this->{$this->getCreatedByColumn()} = $id;
    }

    /**
     * Set the editor of the model to be the initiating user.
     *
     * @param  int|string|null  $id
     * @return void
     */
    public function setUpdatedBy($id)
    {
        $this->{$this->getUpdatedByColumn()} = $id;
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        if (defined('self::CREATED_BY')) {
            return self::CREATED_BY;
        }

        return 'created_by';
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        if (defined('self::UPDATED_BY')) {
            return self::UPDATED_BY;
        }

        return 'updated_by';
    }

    /**
     * Get the user model for the application.
     *
     * @return class-string<Model>
     */
    protected function getAuthorModel()
    {
        /** @var class-string<Model> */
        return Config::get('auth.providers.users.model', User::class);
    }

    /**
     * Get the model which is initiating the action.
     *
     * @return Model|int|string|null
     */
    protected function getTouchedBy()
    {
        return Auth::id();
    }

    /**
     * Get the identifying key of the initiating model to update this model.
     *
     * @return int|string|null
     */
    protected function getTouchedByKey()
    {
        $touchedBy = $this->getTouchedBy();

        if ($touchedBy instanceof Model) {
            /** @var int|string */
            return $touchedBy->getRouteKey();
        }

        return $touchedBy;
    }
}
