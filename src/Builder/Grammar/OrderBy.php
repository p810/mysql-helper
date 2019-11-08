<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\commas;

trait OrderBy
{
    /**
     * Appends an order by clause to the query
     * 
     * @param string $column The column to order by
     * @param string $direction The direction that results should be ordered in (asc/desc)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orderBy(string $column, string $direction = 'desc'): BuilderInterface
    {
        $order = $this->getParameter('order') ?? [];

        $order[] = "$column $direction";

        return $this->setParameter('order', $order);
    }

    /**
     * Compiles the order by clause
     *
     * @return null|string
     */
    protected function compileOrder(): ?string
    {
        $order = $this->getParameter('order');

        if (! $order) {
            return null;
        }
        
        return 'order by ' . commas($order);
    }
}
