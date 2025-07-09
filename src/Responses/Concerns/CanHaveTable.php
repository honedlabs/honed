<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Honed\Contracts\Modelable;
use Honed\Table\Table;

trait CanHaveTable
{
    public const TABLE_PROP = 'table';

    /**
     * The table to be rendered.
     *
     * @var bool|class-string<Table>|Table
     */
    protected $table = false;

    /**
     * Set the table for the response.
     *
     * @param  bool|class-string<Table>|Table  $table
     * @return $this
     */
    public function table(bool|string|Table $table = true): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the table to be rendered.
     */
    public function getTable(): ?Table
    {
        return match (true) {
            is_string($this->table) => ($this->table)::make(),
            $this->table instanceof Table => $this->table,
            $this->table === true && $this instanceof Modelable => $this->getModel()->table(), // @phpstan-ignore-line method.notFound
            default => null,
        };
    }

    /**
     * Convert the table to an array of props.
     *
     * @return array<string, mixed>
     */
    public function canHaveTableToProps(): array
    {
        if ($table = $this->getTable()) {
            return [
                self::TABLE_PROP => $table->toArray(),
            ];
        }

        return [];
    }
}
