<?php

namespace p810\MySQL\Builder;

use PDO;
use PDOStatement;
use p810\MySQL\Builder\Grammar\Expression;

use function array_map;
use function p810\MySQL\commas;
use function p810\MySQL\parentheses;

class Insert extends Builder
{
    /**
     * @inheritdoc
     */
    const COMMAND = 'insert';

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
     * @var \p810\MySQL\Builder\Grammar\Expression[]
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
     * Compiles the into clause of the query
     * 
     * @return null|string
     */
    protected function compileInto(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return "into $this->table";
    }

    /**
     * Begins the query with the insert command
     * 
     * Previously this function included the "into" clause as well, but
     * to support the priority and ignore modifiers, that had to be separated
     * into its own component
     * 
     * @return null|string
     */
    protected function compileInsert(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return 'insert';
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
        if (! $this->ignore) {
            return null;
        }

        return 'ignore';
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
     * Alias for \p810\MySQL\Builder\Insert::onDuplicateKeyUpdate()
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
}
