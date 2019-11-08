<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

trait Ignore
{
    /**
     * Adds the `IGNORE` operator to the query
     * 
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function ignore(): BuilderInterface
    {
        return $this->setParameter('ignore', 'ignore');
    }

    /**
     * Returns the `IGNORE` operator if needed
     * 
     * @return null|string
     */
    protected function compileIgnore(): ?string
    {
        return $this->getParameter('ignore');
    }
}
