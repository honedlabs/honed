<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Illuminate\Support\Str;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
trait HasModel
{
    /**
     * The model to be passed to the view.
     *
     * @var TModel
     */
    protected $model;

    /**
     * The data object to use.
     *
     * @var class-string<\Spatie\LaravelData\Data>|null
     */
    protected $data;

    /**
     * The name of the model prop for the view.
     *
     * @var string|null
     */
    protected $propName;

    /**
     * Set the model for the view.
     *
     * @param  TModel  $value
     * @return $this
     */
    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    /**
     * Get the model for the view.
     *
     * @return TModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the data object for the view.
     *
     * @param  class-string<\Spatie\LaravelData\Data>|null  $value
     * @return $this
     */
    public function data($value)
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Get the data object for the view.
     *
     * @return class-string<\Spatie\LaravelData\Data>|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the name of the model prop for the view.
     *
     * @param  string  $value
     * @return $this
     */
    public function propName($value)
    {
        $this->propName = $value;

        return $this;
    }

    /**
     * Get the name of the model prop for the view.
     *
     * @return string
     */
    public function getPropName()
    {
        return $this->propName ??= $this->guessPropName();
    }

    /**
     * Get the prepared model for the view.
     *
     * @return mixed
     */
    public function getPropModel()
    {
        return $this->prepare($this->getModel());
    }

    /**
     * Guess the name of the model prop for the view.
     *
     * @return string
     */
    protected function guessPropName()
    {
        return Str::of($this->model::class)
            ->classBasename()
            ->singular()
            ->camel()
            ->value();
    }

    /**
     * Prepare the model prop for serialization.
     *
     * @param  TModel  $model
     * @return mixed
     */
    protected function prepare($model)
    {
        if ($data = $this->getData()) {
            return $data::from($model);
        }

        return $model;
    }

    /**
     * Convert the model to an array of props.
     *
     * @return array<string, mixed>
     */
    protected function modelToArray()
    {
        return [$this->getPropName() => $this->getPropModel()];
    }
}
