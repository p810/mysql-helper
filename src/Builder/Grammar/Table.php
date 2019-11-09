<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

trait Table
{
    /**
     * Specifies the source table for the query
     * 
     * @param string $table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function from(string $table): BuilderInterface
    {
        return $this->setParameter('from', $table);
    }

    /**
     * Specifies the source table for an `INSERT` query
     * 
     * @param string $table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function into(string $table): BuilderInterface
    {
        return $this->setParameter('into', $table);
    }

    /**
     * Specifies a table name by itself
     * 
     * @param string $table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function table(string $table): BuilderInterface
    {
        return $this->setParameter('table', $table);
    }

    /**
     * Compiles the query's `FROM` clause
     * 
     * @return null|string
     */
    protected function compileFrom(): ?string
    {
        $from = $this->getParameter('from');

        if (! $from) {
            return null;
        }

        return "from $from";
    }

    /**
     * Compiles the query's `INTO` clause
     * 
     * @return null|string
     */
    protected function compileInto(): ?string
    {
        $into = $this->getParameter('into');

        if (! $into) {
            return null;
        }

        return "into $into";
    }

    /**
     * Compiles the query's table name
     * 
     * @return null|string
     */
    protected function compileTable(): ?string
    {
        return $this->getParameter('table');
    }
}
