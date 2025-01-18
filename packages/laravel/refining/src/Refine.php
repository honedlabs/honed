<?php

declare(strict_types=1);

namespace Honed\Refining;

use Honed\Core\Primitive;
use Illuminate\Http\Request;
use Honed\Refining\Sorts\Sort;
use Honed\Core\Concerns\HasScope;
use Honed\Refining\Filters\Filter;
use Honed\Refining\Contracts\Refines;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static sorts(iterable<\Honed\Refining\Sorts\Sort> $sorts)
 * @method static filters(iterable<\Honed\Refining\Filters\Filter> $filters)
 */
class Refine extends Primitive
{
    use ForwardsCalls;
    use HasScope;
    use Concerns\HasBuilderInstance;
    use Concerns\HasFilters;
    use Concerns\HasSorts;
    use Concerns\HasRequest;

    protected bool $refined = false;

    public function __construct(Request $request)
    {
        $this->request($request);
    }

    public function __call($name, $arguments)
    {
        if ($name === 'sorts') {
            return $this->addSorts($arguments);
        }

        if ($name === 'filters') {
            return $this->addFilters($arguments);
        }

        // Delay the refine call
        $this->refine();

        return $this->forwardDecoratedCallTo($this->getBuilder(), $name, $arguments);
    }

    public static function make(Model|string|Builder $model): static
    {
        return static::query($model);
    }

    /**
     * Refines the given model.
     */
    public static function model(Model|string $model): static
    {
        return static::query($model);
    }

    /**
     * Refines the given query.
     */
    public static function query(Model|string|Builder $query): static
    {
        if ($query instanceof Model) {
            $query = $query::query();
        }

        if (\is_string($query) && class_exists($query) && is_subclass_of($query, Model::class)) {
            $query = $query::query();
        }

        if (!$query instanceof Builder) {
            throw new \InvalidArgumentException('Expected a model class name or a query instance.');
        }

        return resolve(static::class)->builder($query);
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray()
    {
        return [
            'sorts' => $this->getSorts(),
            'filters' => $this->getFilters(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function refinements(): array
    {
        return $this->toArray();
    }

    /**
     * @return $this
     */
    public function refine(): static
    {
        if ($this->refined) {
            return $this;
        }

        $this->sort($this->getBuilder());
        $this->filter($this->getBuilder());

        $this->refined = true;

        return $this;
    }

    /**
     * Add the given filters or sorts to the refine pipeline.
     * 
     * @param iterable<\Honed\Refining\Refiner> $refiners
     * @return $this
     */
    public function with(iterable $refiners): static
    {
        foreach ($refiners as $refiner) {
            match (true) {
                $refiner instanceof Filter => $this->addFilter($refiner),
                $refiner instanceof Sort => $this->addSort($refiner),
            };
        }

        return $this;
    }    
}
