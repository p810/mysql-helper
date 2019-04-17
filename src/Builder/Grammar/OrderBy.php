<?php

namespace p810\MySQL\Builder\Grammar;

use function p810\MySQL\commas;

trait OrderBy
{
    /**
     * @var string[]
     */
    protected $order = [];

    /**
     * Appends an order by clause to the query
     * 
     * @param string $column    The column to order by
     * @param string $direction The direction that results should be ordered in (asc/desc)
     * @return self
     */
    public function orderBy(string $column, string $direction = 'desc'): self
    {
        $this->order[] = "$column $direction";

        return $this;
    }

    /**
     * Compiles the order by clause
     *
     * @return null|string
     */
    protected function compileOrder(): ?string
    {
        if (! $this->order) {
            return null;
        }
        
        return 'order by ' . commas($this->order);
    }
}