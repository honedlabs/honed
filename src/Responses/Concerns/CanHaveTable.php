<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Table\Table;

/**
 * @template TTable of \Honed\Table\Table = \Honed\Table\Table
 */
trait CanHaveTable
{
    public const TABLE_PROP = 'table';

    /**
     * The table to be rendered.
     *
     * @var class-string<TTable>|TTable|null
     */
    protected $table;

    /**
     * Set the table for the response.
     *
     * @param  class-string<TTable>|TTable  $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the table to be rendered.
     *
     * @return TTable|null
     */
    public function getTable()
    {
        return match (true) {
            is_string($this->table) => ($this->table)::make(),
            $this->table instanceof Table => $this->table,
            default => null,
        };
    }

    /**
     * Convert the table to an array of props.
     *
     * @return array<string, mixed>
     */
    protected function tableToArray()
    {
        if ($table = $this->getTable()) {
            return [self::TABLE_PROP => $table];
        }

        return [];
    }
}
