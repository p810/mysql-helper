<?php

namespace p810\MySQL\Builder\Grammar;

trait From
{
    /**
     * @var string
     */
    protected $from;

    /**
     * Specifies the source table for the query
     * 
     * @param string $table
     * @return self
     */
    public function from(string $table): self
    {
        $this->from = $table;

        return $this;
    }

    /**
     * Compiles the query's `FROM` clause
     * 
     * @return null|string
     */
    protected function compileFrom(): ?string
    {
        if (! $this->from) {
            return null;
        }

        return "from $this->from";
    }
}
