<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Responses\Concerns\HasModel;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class EditResponse extends InertiaResponse
{
    /** @use HasModel<TModel> */
    use HasModel;

    public const UPDATE_PROP = 'update';

    /**
     * The route to update the model.
     *
     * @var string
     */
    protected $update;

    /**
     * Create a new edit response.
     *
     * @param  TModel  $model
     * @param  string  $update
     */
    public function __construct($model, $update)
    {
        $this->model($model);
        $this->updateUrl($update);
    }

    /**
     * Set the route to update the model.
     *
     * @param  string  $value
     * @return $this
     */
    public function updateUrl($value)
    {
        $this->update = $value;

        return $this;
    }

    /**
     * Get the route to update the model.
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->update;
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
            self::UPDATE_PROP => $this->getUpdateUrl(),
            $this->getPropName() => $this->getPropModel(),
        ];
    }
}
