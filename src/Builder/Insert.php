<?php

namespace p810\MySQL\Builder;

use PDOStatement;

use function implode;
use function is_array;
use function array_map;
use function array_reduce;
use function p810\MySQL\commas;
use function p810\MySQL\parentheses;

class Insert extends Builder
{
    /**
     * @inheritdoc
     */
    protected $components = [
        'insert',
        'priority',
        'ignore',
        'columns',
        'values',
        'onDuplicateKeyUpdate'
    ];

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $ignore;

    /**
     * @var bool
     */
    protected $updateOnDuplicate;

    /**
     * Specifies the table that the data should be inserted into
     * 
     * @param string $table The table to insert data into
     * @return self
     */
    public function into(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Compiles the insert into clause
     * 
     * @return string
     */
    protected function compileInsert(): string
    {
        return "insert into $this->table";
    }

    /**
     * Specifies that this query should be delayed until all other clients have finished
     * their operations on the specified table
     * 
     * @return self
     */
    public function lowPriority(): self
    {
        $this->priority = 'low_priority';

        return $this;
    }

    /**
     * Overrides `--low-priority-updates` if this option is set in MySQL and disables
     * concurrent updates
     * 
     * @return self
     */
    public function highPriority(): self
    {
        $this->priority = 'high_priority';

        return $this;
    }

    /**
     * Compiles the priority clause of the query
     * 
     * @return null|string
     */
    protected function compilePriority(): ?string
    {
        return $this->priority;
    }

    /**
     * Appends the "ignore" modifier telling MySQL to ignore errors that occur when
     * inserting new rows
     * 
     * @param bool $shouldIgnore
     * @return self
     */
    public function ignore(bool $shouldIgnore = true): self
    {
        $this->ignore = $shouldIgnore;

        return $this;
    }

    /**
     * Compiles the ignore clause of the query
     * 
     * @return null|string
     */
    protected function compileIgnore(): ?string
    {
        return $this->ignore ? 'ignore' : null;
    }

    /**
     * Specifies an "on duplicate key update" clause for the query
     * 
     * @param bool $shouldUpdateOnDuplicate
     * @return self
     */
    public function onDuplicateKeyUpdate(bool $shouldUpdateOnDuplicate = true): self
    {
        $this->updateOnDuplicate = $shouldUpdateOnDuplicate;

        return $this;
    }

    /**
     * Alias for \p810\MySQL\Builder\Insert::onDuplicateKeyUpdate()
     * 
     * @param bool $shouldUpdateOnDuplicate
     * @return self
     */
    public function updateDuplicate(bool $shouldUpdateOnDuplicate): self
    {
        return $this->onDuplicateKeyUpdate($shouldUpdateOnDuplicate);
    }

    /**
     * Compiles the "on duplicate key update" clause
     * 
     * @return null|string
     */
    protected function compileOnDuplicateKeyUpdate(): ?string
    {
        return $this->updateOnDuplicate ? 'on duplicate key update' : null;
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
}