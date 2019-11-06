<?php

namespace p810\MySQL\Builder;

use p810\MySQL\Builder\Grammar\Expression;

use function array_map;
use function array_keys;
use function array_values;
use function p810\MySQL\commas;
use function p810\MySQL\parentheses;

class Insert extends AbstractBuilder
{
    use Grammar\Table;
    use Grammar\Ignore;
    use Grammar\Priority;

    /**
     * @inheritdoc
     */
    protected $components = [
        'insert',
        'priority',
        'ignore',
        'into',
        'columns',
        'values',
        'onDuplicateKeyUpdate'
    ];

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var \p810\MySQL\Builder\Grammar\Expression[]
     */
    protected $updateOnDuplicate;

    /**
     * Returns the `INSERT` keyword
     * 
     * @return string
     */
    protected function compileInsert(): string
    {
        return 'insert';
    }

    /**
     * Specifies an "on duplicate key update" clause for the query
     * 
     * @param string $column
     * @param mixed  $value
     * @return self
     */
    public function onDuplicateKeyUpdate(string $column, $value): self
    {
        $this->updateOnDuplicate[] = new Expression($column, $this->bind($value));

        return $this;
    }

    /**
     * Alias for `\p810\MySQL\Builder\Insert::onDuplicateKeyUpdate()`
     * 
     * @param string $column
     * @param mixed  $value
     * @return self
     */
    public function updateDuplicate(string $column, $value): self
    {
        return $this->onDuplicateKeyUpdate($column, $value);
    }

    /**
     * Compiles the "on duplicate key update" clause
     * 
     * @return null|string
     */
    protected function compileOnDuplicateKeyUpdate(): ?string
    {
        if (! $this->updateOnDuplicate) {
            return null;
        }

        return 'on duplicate key update ' . commas($this->updateOnDuplicate);
    }

    /**
     * Specifies an optional list of columns that corresponds to the inserted values
     * 
     * @param string[] $columns A list of column names
     * @return self
     */
    public function columns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Compiles the column list
     * 
     * @return null|string
     */
    protected function compileColumns(): ?string
    {
        if (! $this->columns) {
            return null;
        }

        return parentheses($this->columns);
    }

    /**
     * Specifies the values to insert into the database
     * 
     * @param array[] $rows An array containing any number of lists of values to insert
     * @return self
     */
    public function values(...$rows): self
    {
        foreach ($rows as $row) {
            $this->values[] = array_map(function ($value) {
                return $this->bind($value);
            }, $row);
        }

        return $this;
    }

    /**
     * Compiles the values clause
     * 
     * @return null|string
     */
    protected function compileValues(): ?string
    {
        if (! $this->values) {
            return null;
        }

        $lists = [];

        foreach ($this->values as $list) {
            $lists[] = parentheses($list);
        }

        return 'values ' . commas($lists);
    }

    /**
     * Sets this query's columns and values from an associative array
     * 
     * @param array<string,mixed> $columnsToValues
     * @return self
     */
    public function setColumnsAndValues(array $columnsToValues): self
    {
        $this->columns(array_keys($columnsToValues));
        
        $this->values(array_values($columnsToValues));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'insert';
    }
}
