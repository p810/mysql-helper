<?php

namespace p810\MySQL\Builder\Grammar;

trait Priority
{
    /**
     * @var null|string
     */
    protected $priority;

    /**
     * Specifies that this query should be delayed until all other clients have finished their operations on the
     * specified table
     * 
     * @return self
     */
    public function lowPriority(): self
    {
        $this->priority = 'low_priority';

        return $this;
    }

    /**
     * Overrides `--low-priority-updates` if this option is set in MySQL and disables concurrent updates
     * 
     * @return self
     */
    public function highPriority(): self
    {
        $this->priority = 'high_priority';

        return $this;
    }

    /**
     * Compiles the priority clause of the query
     * 
     * @return null|string
     */
    protected function compilePriority(): ?string
    {
        return $this->priority;
    }
}
