<?php

namespace p810\MySQL\Builder\Grammar;

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

    protected function setOrderBy(): void
    {
        $this->data['orderBy'] = implode(', ', $this->order);
    }

    protected function hasOrderByClauses(): bool
    {
        return count($this->order) >= 1;
    }
}