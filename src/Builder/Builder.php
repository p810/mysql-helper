<?php

namespace p810\MySQL\Builder;

use OutOfBoundsException;

use function implode;
use function is_array;
use function array_map;
use function array_key_exists;

abstract class Builder
{
    /**
     * @var array
     */
    public $bindings;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string|int|array $value
     * @return string|string[]
     */
    public function bind($value)
    {
        if (is_array($value)) {
            return array_map(function ($value) {
                return $this->bind($value);
            }, $value);
        }

        $this->bindings[] = $value;
        
        return '?';
    }

    /**
     * @return mixed
     * @throws \OutOfBoundsException if the given key is not set in Builder::$data
     */
    public function getData(string $key)
    {
        if (! array_key_exists($key, $this->data)) {
            throw new OutOfBoundsException;
        }
        
        return $this->data[$key];
    }

    protected function setTable(string $table): void
    {
        $this->data['table'] = $table;
    }

    public function from(string $table): self
    {
        $this->setTable($table);
        return $this;
    }

    protected function setColumns($columns): void
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        
        $this->data['columns'] = $columns;
    }

    public function columns($columns): self
    {
        $this->setColumns($columns);
        return $this;
    }
}