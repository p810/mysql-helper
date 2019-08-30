<?php

namespace p810\MySQL\Builder;

use PDOStatement;
use InvalidArgumentException;
use p810\MySQL\Builder\Grammar\Expression;

use function is_array;
use function p810\MySQL\commas;

class Replace extends Insert
{
    /**
     * @inheritdoc
     */
    const COMMAND = 'replace';

    /**
     * @inheritdoc
     */
    protected $components = [
        'replace',
        'priority',
        'into',
        'columns',
        'values'
    ];

    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    public function compilePriority(): ?string
    {
        if ($this->priority && $this->priority !== 'low_priority') {
            throw new InvalidArgumentException(
                'REPLACE queries only support the LOW_PRIORITY modifier'
            );
        }

        return parent::compilePriority();
    }

    /**
     * Returns the query command
     * 
     * @return null|string
     */
    protected function compileReplace(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return 'replace';
    }

    /**
     * Sets values for an assignment list
     * 
     * If the first argument is an array, it will expect it to be associative
     * and iterate over it to call \p810\MySQL\Builder\Replace::set() for each
     * column => value pair. If two arguments are passed then it will append an
     * instance of p810\MySQL\Builder\Grammar\Expression to the assignment list.
     * 
     * @param array $arguments A variadic list of arguments
     * @return self
     */
    public function set(...$arguments): self
    {
        if (is_array($arguments[0])) {
            foreach ($arguments[0] as $column => $value) {
                $this->set($column, $value);
            }
        } else {
            [$column, $value] = $arguments;

            $this->values[] = new Expression($column, $this->bind($value));
        }

        return $this;
    }

    /**
     * Checks whether this query is using an assignment list or value list
     * and returns a string accordingly
     * 
     * @return null|string
     */
    public function compileValues(): ?string
    {
        if (! is_array($this->values)) {
            return null;
        }

        if ($this->values[0] instanceof Expression) {
            return $this->compileSetValues();
        }

        return parent::compileValues();
    }

    /**
     * Compiles an assignment list for a "set (...)" clause
     * 
     * @return string
     */
    protected function compileSetValues(): string
    {
        return 'set ' . commas($this->values);
    }
}
