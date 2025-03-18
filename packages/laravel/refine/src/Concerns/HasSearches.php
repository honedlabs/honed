<?php

declare(strict_types=1);

namespace Honed\Refine\Concerns;

use Honed\Core\Interpreter;
use Honed\Refine\Search;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
trait HasSearches
{
    /**
     * List of the searches.
     *
     * @var array<int,\Honed\Refine\Search<TModel, TBuilder>>|null
     */
    protected $searches;

    /**
     * The query parameter to identify the search string.
     *
     * @var string|null
     */
    protected $searchesKey;

    /**
     * Whether the search columns can be toggled.
     *
     * @var bool|null
     */
    protected $match;

    /**
     * The query parameter to identify the columns to search on.
     *
     * @var string|null
     */
    protected $matchesKey;

    /**
     * The search term as a string without replacements.
     *
     * @var string|null
     */
    protected $term;

    /**
     * Whether to apply the searches.
     *
     * @var bool
     */
    protected $searching = true;

    /**
     * Whether to not provide the searches.
     *
     * @var bool
     */
    protected $withoutSearches = false;

    /**
     * Merge a set of searches with the existing searches.
     *
     * @param  array<int, \Honed\Refine\Search<TModel, TBuilder>>|\Illuminate\Support\Collection<int, \Honed\Refine\Search<TModel, TBuilder>>  $searches
     * @return $this
     */
    public function addSearches($searches)
    {
        if ($searches instanceof Collection) {
            $searches = $searches->all();
        }

        $this->searches = \array_merge($this->searches ?? [], $searches);

        return $this;
    }

    /**
     * Add a single search to the list of searches.
     *
     * @param  \Honed\Refine\Search<TModel, TBuilder>  $search
     * @return $this
     */
    public function addSearch($search)
    {
        $this->searches[] = $search;

        return $this;
    }

    /**
     * Retrieve the columns to be used for searching.
     *
     * @return array<int,\Honed\Refine\Search<TModel, TBuilder>>
     */
    public function getSearches()
    {
        return once(function () {

            $searches = \method_exists($this, 'searches') ? $this->searches() : [];

            $searches = \array_merge($searches, $this->searches ?? []);

            return \array_values(
                \array_filter(
                    $searches,
                    static fn (Search $search) => $search->isAllowed()
                )
            );
        });
    }

    /**
     * Determines if the instance has any searches.
     *
     * @return bool
     */
    public function hasSearch()
    {
        return filled($this->getSearches());
    }

    /**
     * Set the query parameter to identify the search string.
     *
     * @param  string  $searchesKey
     * @return $this
     */
    public function searchesKey($searchesKey)
    {
        $this->searchesKey = $searchesKey;

        return $this;
    }

    /**
     * Get the query parameter to identify the search.
     *
     * @return string
     */
    public function getSearchesKey()
    {
        return $this->searchesKey ?? static::fallbackSearchesKey();
    }

    /**
     * Get the query parameter to identify the search from the config.
     *
     * @return string
     */
    public static function fallbackSearchesKey()
    {
        return type(config('refine.searches_key', 'search'))->asString();
    }

    /**
     * Set the query parameter to identify the columns to search.
     *
     * @param  string  $matchesKey
     * @return $this
     */
    public function matchesKey($matchesKey)
    {
        $this->matchesKey = $matchesKey;

        return $this;
    }

    /**
     * Get the query parameter to identify the columns to search.
     *
     * @return string
     */
    public function getMatchesKey()
    {
        return $this->matchesKey ?? static::fallbackMatchesKey();
    }

    /**
     * Get the query parameter to identify the columns to search from the config.
     *
     * @return string
     */
    public static function fallbackMatchesKey()
    {
        return type(config('refine.matches_key', 'match'))->asString();
    }

    /**
     * Set whether the search columns can be toggled.
     *
     * @param  bool|null  $match
     * @return $this
     */
    public function match($match = true)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Determine if matching is enabled
     *
     * @return bool
     */
    public function isMatching()
    {
        return (bool) ($this->match ?? static::fallbackMatching());
    }

    /**
     * Determine if matching is enabled from the config.
     *
     * @return bool
     */
    public static function fallbackMatching()
    {
        return (bool) config('refine.match', false);
    }

    /**
     * Set the instance to apply the searches.
     *
     * @param  bool  $searching
     * @return $this
     */
    public function searching($searching = true)
    {
        $this->searching = $searching;

        return $this;
    }

    /**
     * Determine if the instance should apply the searches.
     *
     * @return bool
     */
    public function isSearching()
    {
        return $this->searching;
    }

    /**
     * Set the instance to not provide the searches.
     *
     * @param  bool  $withoutSearches
     * @return $this
     */
    public function withoutSearches($withoutSearches = true)
    {
        $this->withoutSearches = $withoutSearches;

        return $this;
    }

    /**
     * Determine if the instance should not provide the searches.
     *
     * @return bool
     */
    public function isWithoutSearches()
    {
        return $this->withoutSearches;
    }

    /**
     * Set the search term.
     * 
     * @param  string|null  $term
     * @return $this
     */
    public function term($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Retrieve the search value.
     *
     * @return string|null
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Get the searches as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function searchesToArray()
    {
        if ($this->isWithoutSearches() || ! $this->isMatching()) {
            return [];
        }

        return \array_map(
            static fn (Search $search) => $search->toArray(),
            $this->getSearches()
        );
    }
}
