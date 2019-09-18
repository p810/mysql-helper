<?php

namespace p810\MySQL\Builder\Grammar;

trait Table
{
    /**
     * @var string
     */
    protected $from;
    
    /**
     * @var string
     */
    protected $into;

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
     * Specifies the source table for an `INSERT` query
     * 
     * @param string $table
     * @return self
     */
    public function into(string $table): self
    {
        $this->into = $table;

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

    /**
     * Compiles the query's `INTO` clause
     * 
     * @return null|string
     */
    protected function compileInto(): ?string
    {
        if (! $this->into) {
            return null;
        }

        return "into $this->into";
    }
}
