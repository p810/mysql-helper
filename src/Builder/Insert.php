<?php

namespace p810\MySQL\Builder;

use PDOStatement;

use function implode;
use function is_array;
use function array_map;
use function array_reduce;

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

        return '(' . implode(', ', $this->columns) . ')';
    }

    public function values(array $values): self
    {
        if (is_array($values[0])) {
            return $this->addMultipleValues($values);
        }

        $this->values[] = array_map(function ($value) {
            return $this->bind($value);
        }, $values);

        return $this;
    }

    protected function compileValues(): string
    {
        $lists = [];

        foreach ($this->values as $list) {
            $lists[] = '(' . implode(', ', $list) . ')';
        }

        return 'values ' . implode(', ', $lists);
    }

    protected function addMultipleValues(array $values): self
    {
        foreach ($values as $list) {
            $this->values($list);
        }

        return $this;
    }
}