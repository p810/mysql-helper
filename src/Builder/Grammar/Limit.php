<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

trait Limit
{
    /**
     * Specifies a limit of rows to return in the result set
     * 
     * @param int $limit The maximum number of rows to return
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function limit(int $limit): BuilderInterface
    {
        return $this->setParameter('limit', $limit);
    }

    /**
     * Compiles the limit clause
     * 
     * @return null|string
     */
    protected function compileLimit(): ?string
    {
        $limit = $this->getParameter('limit');

        if ($limit === null) {
            return null;
        }

        return "limit $limit";
    }
}
