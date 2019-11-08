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
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function onDuplicateKeyUpdate(string $column, $value): BuilderInterface
    {
        $values = $this->getParameter('updateOnDuplicate') ?? [];
        
        $values[] = new Expression($column, $this->bind($value));

        return $this->setParameter('updateOnDuplicate', $values);
    }

    /**
     * Alias for `\p810\MySQL\Builder\Insert::onDuplicateKeyUpdate()`
     * 
     * @param string $column
     * @param mixed  $value
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function updateDuplicate(string $column, $value): BuilderInterface
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
        $updateOnDuplicate = $this->getParameter('updateOnDuplicate');

        if (! $updateOnDuplicate) {
            return null;
        }

        return 'on duplicate key update ' . commas($updateOnDuplicate);
    }

    /**
     * Specifies an optional list of columns that corresponds to the inserted values
     * 
     * @param string[] $columns A list of column names
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function columns(array $columns): BuilderInterface
    {
        return $this->setParameter('columns', $columns);
    }

    /**
     * Compiles the column list
     * 
     * @return null|string
     */
    protected function compileColumns(): ?string
    {
        $columns = $this->getParameter('columns');

        if (! $columns) {
            return null;
        }

        return parentheses($columns);
    }

    /**
     * Specifies the values to insert into the database
     * 
     * @param array[] $rows An array containing any number of lists of values to insert
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function values(...$rows): BuilderInterface
    {
        $values = $this->getParameter('values') ?? [];

        foreach ($rows as $row) {
            $values[] = array_map(function ($value) {
                return $this->bind($value);
            }, $row);
        }

        return $this->setParameter('values', $values);
    }

    /**
     * Compiles the values clause
     * 
     * @return null|string
     */
    protected function compileValues(): ?string
    {
        $values = $this->getParameter('values');

        if (! $values) {
            return null;
        }

        $values = array_map(function (array $list): string {
            return parentheses($list);
        }, $values);

        return 'values ' . commas($values);
    }

    /**
     * Sets this query's columns and values from an associative array
     * 
     * @param array<string,mixed> $columnsToValues
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function setColumnsAndValues(array $columnsToValues): BuilderInterface
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
