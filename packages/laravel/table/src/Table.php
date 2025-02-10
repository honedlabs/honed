<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Refine\Refine;
use Honed\Table\Columns\Column;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\RequiresKey;
use Honed\Action\Concerns\HasActions;
use Illuminate\Database\Eloquent\Builder;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
use Honed\Table\Concerns\HasTableBindings;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Table extends Refine implements UrlRoutable
{
    use Concerns\ConfiguresKeys;
    use Concerns\HasColumns;
    use Concerns\HasEndpoint;
    use Concerns\HasModifier;
    use Concerns\HasRecords;
    use Concerns\HasResource;
    use Concerns\HasToggle;
    use Encodable;
    use HasActions;
    use RequiresKey;
    use HasTableBindings;

    /**
     * @param \Closure|null $modifier
     */
    public static function make($modifier = null): static
    {
        return resolve(static::class)
            ->modifier($modifier);
    }

    /**
     * Get the key name for the table records.
     *
     * @throws \Honed\Core\Exceptions\MissingRequiredAttributeException
     */
    public function getKeyName(): string
    {
        try {
            return $this->getKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()
                ?->getName() ?? throw $e;
        }
    }

    /**
     * Build the table using the given request.
     * 
     * @return $this
     */
    public function buildTable(): static
    {
        if ($this->isRefined()) {
            return $this;
        }

        $this->builder(
            $this->createBuilder($this->getResource())
        );
        
        $activeColumns = $this->toggle();
        
        $this->modify();
        
        $this->refine();
        
        $this->formatAndPaginate($activeColumns);

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $this->buildTable();

        return [
            'table' => $this->getRouteKey(),
            'records' => $this->getRecords(),
            'meta' => $this->getMeta(),
            'columns' => $this->getActiveColumns(),
            'pages' => $this->getPages(),
            'filters' => $this->getFilters(),
            'sorts' => $this->getSorts(),
            'toggle' => $this->isToggleable(),
            'actions' => $this->actionsToArray(),
            'endpoint' => $this->getEndpoint(),
            'keys' => $this->keysToArray(),
        ];
    }

    /**
     * Get the keys for the table as an array.
     * 
     * @return array<string,string>
     */
    public function keysToArray(): array
    {
        return [
            'record' => $this->getKeyName(),
            'records' => $this->getRecordsKey(),
            'sorts' => $this->getSortKey(),
            'search' => $this->getSearchKey(),
            'columns' => $this->getColumnsKey(),
            ...($this->hasMatches() ? ['match' => $this->getMatchKey()] : []),
        ];
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'builder' => [$this->getResource()],
            'resource' => [$this->getResource()],
            'query' => [$this->getResource()],
            'request' => [$this->getRequest()],
            default => [],
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            Builder::class => [$this->getResource()],
            Model::class => [$this->getResource()],
            Request::class => [$this->getRequest()],
            default => [],
        };
    }
}
