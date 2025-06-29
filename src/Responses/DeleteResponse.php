<?php

declare(strict_types=1);

namespace Honed\Honed\Responses;

use Honed\Honed\Responses\Concerns\HasModel;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class DeleteResponse extends InertiaResponse
{
    /** @use HasModel<TModel> */
    use HasModel;

    public const DESTROY_PROP = 'destroy';

    /**
     * The route to destroy the model.
     *
     * @var string
     */
    protected $destroy;

    /**
     * Create a new edit response.
     *
     * @param  TModel  $model
     * @param  string  $destroyUrl
     */
    public function __construct($model, $destroyUrl)
    {
        $this->model($model);
        $this->destroyUrl($destroyUrl);
    }

    /**
     * Set the route to destroy the model.
     *
     * @param  string  $value
     * @return $this
     */
    public function destroyUrl($value)
    {
        $this->destroy = $value;

        return $this;
    }

    /**
     * Get the route to destroy the model.
     *
     * @return string
     */
    public function getDestroyUrl()
    {
        return $this->destroy;
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
            self::DESTROY_PROP => $this->getDestroyUrl(),
            $this->getPropName() => $this->getPropModel(),
        ];
    }
}
