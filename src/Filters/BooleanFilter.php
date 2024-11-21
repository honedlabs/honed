<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Honed\Table\Filters\Concerns\HasClause;
use Honed\Table\Filters\Concerns\HasOperator;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;

class BooleanFilter extends PropertyFilter
{
    use HasClause;
    use HasOperator;

    public function setUp(): void
    {
        $this->setType('boolean');
        $this->setClause(Clause::Is);
        $this->setOperator(Operator::Equal);
    }

    public function getValueFromRequest(): mixed
    {
        return Request::boolean($this->getName());
    }

    public function apply(Builder|QueryBuilder $builder): void
    {
        $this->setActive($this->getValueFromRequest());
        $builder->when(
            $this->isActive(),
            fn (Builder|QueryBuilder $builder) => $this->handle($builder),
        );
    }

    public function handle(Builder|QueryBuilder $builder): void
    {
        $this->getClause()
            ->apply($builder,
                $this->getProperty(),
                $this->getOperator(),
                $this->getValue()
            );
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        unset($array['value']);

        return $array;
    }
}
