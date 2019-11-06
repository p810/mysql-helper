<?php

namespace p810\MySQL\Builder\Grammar;

use function p810\MySQL\commas;

trait Set
{
    /**
     * @var array<int,\p810\MySQL\Builder\Grammar\Expression>
     */
    protected $setValues = [];

    /**
     * Specifies a column to set and its new value
     * 
     * @param string $column
     * @param mixed $value
     * @return self
     */
    public function set(string $column, $value): self
    {
        $this->setValues[] = new Expression($column, $this->bind($value));

        return $this;
    }

    /**
     * Specifies multiple columns to update, and their respective values
     * 
     * @param array<string,mixed> $arguments An associative array mapping columns to values
     * @return self
     */
    public function setMany(array $arguments): self
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
        if (! $this->setValues) {
            return null;
        }

        return 'set ' . commas($this->setValues);
    }
}
