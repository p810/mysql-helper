<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\commas;

trait Table
{
    /**
     * Specifies the source table for the query
     * 
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The name of the table to pull data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function from($table, ?string $alias = null): BuilderInterface
    {
        $from = $this->getParameter('from') ?? [];

        $from[] = $table = $this->prepare($table);

        if ($alias) {
            $this->alias($alias, $table);
        }

        $this->setCurrentTable($table);

        return $this->setParameter('from', $from);
    }

    /**
     * Specifies multiple tables for the query
     * 
     * If an item in the given array has a string key, the key will be used as an alias for the table
     * 
     * @param array<int|string,string|\p810\MySQL\Builder\BuilderInterface> $tables A list of tables to set
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function fromMany(array $tables): BuilderInterface
    {
        foreach ($tables as $alias => $table) {
            $arguments = [$table];

            if (is_string($alias)) {
                $arguments[] = $alias;
            }

            $this->from(...$arguments);
        }

        return $this;
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

        if ($from !== null) {
            foreach ($from as &$table) {
                $alias = $this->getTableAlias($table);

                if ($alias) {
                    $table = "$table as $alias";
                }
            }

            $from = 'from ' . commas($from);
        }

        return $from;
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
