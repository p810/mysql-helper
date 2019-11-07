<?php

namespace p810\MySQL\Builder\Grammar;

trait Limit
{
    /**
     * @var null|int
     */
    protected $limit;

    /**
     * Specifies a limit of rows to return in the result set
     * 
     * @param int $limit The maximum number of rows to return
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Compiles the limit clause
     * 
     * @return null|string
     */
    protected function compileLimit(): ?string
    {
        if (! $this->limit) {
            return null;
        }

        return "limit $this->limit";
    }
}
