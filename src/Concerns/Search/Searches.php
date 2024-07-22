<?php

namespace Conquest\Table\Concerns\Search;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait Searches
{
    use UsesScout;
    use HasSearch;
    use HasSearchKey;

    public function search(Builder|QueryBuilder $query, array $fields): void
    {
        if ($this->lacksSearch() && !$this->usesScout()) return;

        if (is_null($q = $this->getSearchFromRequest())) return;


        if ($this->usesScout()) {
            $query->search($q); // @phpstan-ignore-line
        } else {
            $query->whereAny(
                $this->getSearch(), 
                'LIKE', 
                "%$q%"
            );
        }
    }
}
