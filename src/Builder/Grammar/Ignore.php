<?php

namespace p810\MySQL\Builder\Grammar;

trait Ignore
{
    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * Adds the `IGNORE` operator to the query
     * 
     * @return self
     */
    public function ignore(): self
    {
        $this->ignore = true;

        return $this;
    }

    /**
     * Returns the `IGNORE` operator if needed
     * 
     * @return null|string
     */
    protected function compileIgnore(): ?string
    {
        if (! $this->ignore) {
            return null;
        }

        return 'ignore';
    }
}
