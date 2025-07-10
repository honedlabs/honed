<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
trait HasModel
{
    /**
     * Whether to not pass the model to the view.
     *
     * @var bool
     */
    protected $serializeModel = true;

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
     * Set whether to pass the model to the view.
     *
     * @return $this
     */
    public function serializeModel(bool $value = true): static
    {
        $this->serializeModel = $value;

        return $this;
    }

    /**
     * Set whether to not pass the model to the view.
     *
     * @return $this
     */
    public function dontSerializeModel(bool $value = true): static
    {
        return $this->serializeModel(! $value);
    }

    /**
     * Get whether to pass the model to the view.
     *
     * @return bool
     */
    public function isSerializingModel(): bool
    {
        return $this->serializeModel;
    }

    /**
     * Get whether to not pass the model to the view.
     *
     * @return bool
     */
    public function isNotSerializingModel(): bool
    {
        return ! $this->serializeModel;
    }

    /**
     * Set the model for the view.
     *
     * @param  TModel  $value
     * @return $this
     */
    public function model(Model $value): static
    {
        $this->model = $value;

        return $this;
    }

    /**
     * Get the model for the view.
     *
     * @return TModel
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set the data object for the view.
     *
     * @param  class-string<\Spatie\LaravelData\Data>|null  $value
     * @return $this
     */
    public function data(?string $value): static
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Get the data object for the view.
     *
     * @return class-string<\Spatie\LaravelData\Data>|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * Set the name of the model prop for the view.
     *
     * @return $this
     */
    public function propName(string $value): static
    {
        $this->propName = $value;

        return $this;
    }

    /**
     * Get the name of the model prop for the view.
     */
    public function getPropName(): string
    {
        return $this->propName ??= $this->guessPropName();
    }

    /**
     * Get the prepared model for the view.
     */
    public function getPropModel(): mixed
    {
        return $this->prepare($this->getModel());
    }

    /**
     * Convert the model to an array of props.
     *
     * @return array<string, mixed>
     */
    public function hasModelToProps(): array
    {
        if ($this->isSerializingModel()) {
            return [
                $this->getPropName() => $this->getPropModel(),
            ];
        }

        return [];
    }

    /**
     * Guess the name of the model prop for the view.
     */
    protected function guessPropName(): string
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
     */
    protected function prepare(Model $model): mixed
    {
        if ($data = $this->getData()) {
            return $data::from($model);
        }

        return $model;
    }
}
