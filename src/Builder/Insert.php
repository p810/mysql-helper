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

    public function into(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    protected function compileInsert(): string
    {
        return "insert into $this->table";
    }

    public function columns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    protected function compileColumns(): ?string
    {
        if (! $this->columns) {
            return null;
        }

        return parentheses($this->columns);
    }

    public function values(...$rows): self
    {
        foreach ($rows as $row) {
            $this->values[] = array_map(function ($value) {
                return $this->bind($value);
            }, $row);
        }

        return $this;
    }

    protected function compileValues(): string
    {
        $lists = [];

        foreach ($this->values as $list) {
            $lists[] = parentheses($list);
        }

        return 'values ' . commas($lists);
    }
}