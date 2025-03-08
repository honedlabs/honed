<?php

declare(strict_types=1);

namespace Honed\Refine\Filters;

use Closure;
use Honed\Refine\Refiner;
use BadMethodCallException;
use Honed\Refine\Refinement;
use Illuminate\Http\Request;
use Honed\Core\Concerns\HasScope;
use Illuminate\Support\Collection;
use Honed\Core\Concerns\Validatable;
use Honed\Refine\Concerns\HasQueryExpression;
use Illuminate\Database\Eloquent\Builder;
use Honed\Refine\Concerns\InterpretsRequest;
use Honed\Refine\Filters\Concerns\HasOptions;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
 */
class Filter extends Refiner
{
    use HasScope;
    use Validatable;
    use HasOptions {
        multiple as protected setMultiple;
    }
    use InterpretsRequest;
    use HasQueryExpression {
        __call as queryCall;
    }

    /**
     * The operator to use for the filter.
     * 
     * @var string
     */
    protected $operator = '=';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type('filter');
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->hasValue();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return \array_merge(parent::toArray(), [
            'value' => $this->getValue(),
            'options' => $this->optionsToArray(),
            'multiple' => $this->isMultiple(),
        ]);
    }

    /**
     * Allow multiple values to be used.
     * 
     * @return $this
     */
    public function multiple()
    {
        $this->setMultiple();
        $this->asArray();
        $this->type('select');

        return $this;
    }

    /**
     * Determine if the value is invalid.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public function invalidValue($value)
    {
        return ! $this->isActive() || 
            ! $this->validate($value) ||
            ($this->hasOptions() && empty($value));
    }

    /**
     * Filter the builder using the request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function apply($builder, $request)
    {
        $parameter = $this->getParameter();
        $key = $this->formatScope($parameter);
        $value = $this->interpret($request, $key);

        $this->value($value);

        if ($this->hasOptions()) {
            $value = $this->activateOptions($value);
        }

        if ($this->invalidValue($value)) {
            return false;
        }

        match (true) {
            $this->hasQueryExpression() => $this->expressQuery($builder, ['value' => $value]),
            default => $this->handle($builder, $value),
        };

        return true;
    }

    /**
     * Handle the filter using a default refinement.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  mixed  $value
     * @return void
     */
    protected function handle($builder, $value)
    {
        $column = $builder->qualifyColumn($this->getName());
        $operator = $this->getOperator();

        $statement = match (true) {
            \in_array($operator, 
                ['like', 'not like', 'ilike', 'not ilike']
            ) => $builder->whereRaw("LOWER({$column}) {$operator} ?", ['%'.\mb_strtolower($value).'%']),

            $this->isMultiple(),
            $this->interpretsArray() => $builder->whereIn($column, $value),

            $this->interpretsDate() => $builder->whereDate($column, $operator, $value),

            $this->interpretsTime() => $builder->whereTime($column, $operator, $value),

            default => $builder->where($column, $operator, $value),
        };
    }

    /**
     * Get the operator to use for the filter.
     * 
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set the operator to use for the filter.
     * 
     * @param  string  $operator
     * @return $this
     */
    public function operator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Dynamically handle calls to the class.
     * 
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try {
            return parent::__call($method, $parameters);
        } catch (BadMethodCallException $e) {
            return $this->queryCall($method, $parameters);
        }
    }
}
