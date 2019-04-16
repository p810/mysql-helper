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
        'columns',
        'values'
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
     * @return string
     */
    protected function compileValues(): string
    {
        $lists = [];

        foreach ($this->values as $list) {
            $lists[] = parentheses($list);
        }

        return 'values ' . commas($lists);
    }
}