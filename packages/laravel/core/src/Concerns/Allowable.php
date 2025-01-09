<?php

declare(strict_types=1);

namespace Honed\Core\Concerns;

trait Allowable
{
    use EvaluableDependency {
        evaluateModelForTrait as evaluateModelForAllowable;
    }
    
    /**
     * @var \Closure|bool
     */
    protected $allow = true;

    /**
     * Set the allow condition for the instance.
     * 
     * @param \Closure|bool $allow The allow condition to be set.
     * @return $this
     */
    public function allow($allow)
    {
        $this->allow = $allow;

        return $this;
    }

    /**
     * Determine if the instance allows the given parameters.
     * 
     * @param array<string,mixed> $named The named parameters to inject into the allow condition, if provided.
     * @param array<string,mixed> $typed The typed parameters to inject into the allow condition, if provided.
     * @return bool True if the allow condition evaluates to true, false otherwise.
     */
    public function allows($named = [], $typed = [])
    {
        $evaluated = (bool) $this->evaluate($this->allow, $named, $typed);

        $this->allow = $evaluated;

        return $evaluated;
    }

    /**
     * Determine if the instance allows the given model using generated closure parameters to be injected.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model The model to check.
     * @return bool True if the allow condition evaluates to true, false otherwise.
     */
    public function allowsModel($model)
    {
        return $this->evaluateModelForAllowable($model, 'allows');
    }
}