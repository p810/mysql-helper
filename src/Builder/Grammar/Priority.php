<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

trait Priority
{
    /**
     * Specifies that this query should be delayed until all other clients have finished their operations on the
     * specified table
     * 
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function lowPriority(): BuilderInterface
    {
        return $this->setParameter('priority', 'low_priority');
    }

    /**
     * Overrides `--low-priority-updates` if this option is set in MySQL and disables concurrent updates
     * 
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function highPriority(): BuilderInterface
    {
        return $this->setParameter('priority', 'high_priority');
    }

    /**
     * Compiles the priority clause of the query
     * 
     * @return null|string
     */
    protected function compilePriority(): ?string
    {
        return $this->getParameter('priority');
    }
}
