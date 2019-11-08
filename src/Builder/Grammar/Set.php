<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\commas;

trait Set
{
    /**
     * Specifies a column to set and its new value
     * 
     * @param string $column
     * @param mixed $value
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function set(string $column, $value): BuilderInterface
    {
        $set = $this->getParameter('set') ?? [];

        $set[] = new Expression($column, $this->bind($value));

        return $this->setParameter('set', $set);
    }

    /**
     * Specifies multiple columns to update, and their respective values
     * 
     * @param array<string,mixed> $arguments An associative array mapping columns to values
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function setMany(array $arguments): BuilderInterface
    {
        foreach ($arguments as $column => $value) {
            $this->set($column, $value);
        }

        return $this;
    }

    /**
     * Compiles an assignment list for the "set (...)" clause
     * 
     * @return null|string
     */
    protected function compileSet(): ?string
    {
        $set = $this->getParameter('set');

        if (! $set) {
            return null;
        }

        return 'set ' . commas($set);
    }
}
