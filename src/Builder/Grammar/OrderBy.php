<?php

namespace p810\MySQL\Builder\Grammar;

use function count;
use function implode;

trait OrderBy
{
    /**
     * @var string[]
     */
    protected $order = [];

    public function orderBy(string $column, string $direction = 'desc'): self
    {
        $this->order[] = "$column $direction";

        return $this;
    }

    protected function compileOrder(): ?string
    {
        if (! $this->order) {
            return null;
        }
        
        return 'order by ' . implode(', ', $this->order);
    }
}