<?php

declare(strict_types=1);

namespace Honed\Refine;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Refiner<TModel, TBuilder>
 */
class Search extends Refiner
{
    /**
     * The query boolean to use for the search.
     *
     * @var 'and'|'or'
     */
    protected $boolean = 'and';

    /**
     * Whether to use a full-text search.
     *
     * @var bool|null
     */
    protected $fullText;

    /**
     * Set the query boolean to use for the search.
     *
     * @param  'and'|'or'  $boolean
     * @return $this
     */
    public function boolean($boolean)
    {
        $this->boolean = $boolean;

        return $this;
    }

    /**
     * Get the query boolean.
     *
     * @return 'and'|'or'
     */
    public function getBoolean()
    {
        return $this->boolean;
    }

    /**
     * Set whether to use a full-text search.
     *
     * @param  bool|null  $fullText
     * @return $this
     */
    public function fullText($fullText = true)
    {
        $this->fullText = $fullText;

        return $this;
    }

    /**
     * Determine if the search is a full-text search.
     *
     * @return bool|null
     */
    public function isFullText()
    {
        if (isset($this->fullText)) {
            return $this->fullText;
        }

        return static::isFullTextByDefault();
    }

    /**
     * Determine if the search is a full-text search by default.
     *
     * @return bool
     */
    public static function isFullTextByDefault()
    {
        return config('refine.full_text', false);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type('search');
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings($value)
    {
        return \array_merge(parent::getBindings($value), [
            'boolean' => $this->getBoolean(),
        ]);
    }

    /**
     * Add the search query scope to the builder.
     *
     * @param  TBuilder  $builder
     * @param  string  $value
     * @param  string  $column
     * @param  string  $boolean
     * @return void
     */
    public function defaultQuery($builder, $value, $column, $boolean = 'and')
    {
        $column = $builder->qualifyColumn($column);
        $sql = \sprintf('LOWER(%s) LIKE ?', $column);
        $binding = ['%'.\mb_strtolower($value, 'UTF8').'%'];
        $builder->whereRaw($sql, $binding, $boolean);
    }
}
