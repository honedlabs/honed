<?php

namespace Honed\Refine\Concerns;

trait HasQualifier
{
    /**
     * Whether to qualify against the builder.
     *
     * @var bool|string
     */
    protected $qualify = true;

    /**
     * Set whether to qualify against the builder.
     *
     * @param  bool|string  $qualify
     * @return $this
     */
    public function qualify($qualify = true)
    {
        $this->qualify = $qualify;

        return $this;
    }

    /**
     * Get the qualifier.
     *
     * @return bool|string
     */
    public function getQualifier()
    {
        return $this->qualify;
    }

    /**
     * Determine if the instance should qualify against the builder.
     *
     * @return bool
     */
    public function qualifies()
    {
        return (bool) $this->qualify;
    }

    /**
     * Get the qualified name.
     *
     * @param  string  $column
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|null  $builder
     * @return string
     */
    public function qualifyColumn($column, $builder = null)
    {
        $qualifier = $this->getQualifier();

        if (! $qualifier) {
            return $column;
        }

        if (\is_string($qualifier) && ! \str_contains($column, '.')) {
            $column = \rtrim($qualifier, '.').'.'.$column;
        }

        return $builder
            ? $builder->qualifyColumn($column)
            : $column;
    }
}
