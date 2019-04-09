<?php

namespace p810\MySQL\Builder\Grammar;

use function count;
use function implode;

trait OrderBy
{
    /**
     * @var array
     */
    protected $order = [];

    public function orderBy(string $column, string $direction = 'DESC'): self
    {
        $this->order[] = "$column $direction";

        return $this;
    }

    protected function getOrderBy(): string
    {
        return implode(', ', $this->order);
    }

    protected function hasOrderByClauses(): bool
    {
        return count($this->order) >= 1;
    }
}