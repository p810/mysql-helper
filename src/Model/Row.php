<?php

namespace p810\MySQL\Model;

use p810\MySQL\Query;
use p810\MySQL\Model;
use p810\MySQL\Exception\ModelException;

use function array_key_exists;

class Row
{
    function __construct(array $data, Model $model)
    {
        $this->data  = $data;
        $this->model = $model;
    }

    public function update(): bool
    {
        $table = $this->model->getTable();
        $primaryKey = $this->model->getPrimaryKey();

        if (! $primaryKey) {
            throw new ModelException('Row::save() failed: No primary key is set for the model');
        }

        $result = Query::update($table)
            ->set($this->data)
            ->where($primaryKey, $this->data[$primaryKey])
            ->execute();
        
        return (bool) $result;
    }

    public function save(): bool
    {
        return $this->update();
    }

    public function delete(): bool
    {
        $primaryKey = $this->model->getPrimaryKey();

        if (! $primaryKey) {
            throw new ModelException('Row::delete() failed: No primary key is set for the model');
        }

        $deleted = Query::delete()
            ->from( $this->model->getTable() )
            ->where($primaryKey, $this->data[$primaryKey])
            ->execute();

        return (bool) $deleted;
    }

    public function getColumn(string $column)
    {
        if (! array_key_exists($column, $this->data)) {
            return null;
        }

        return $this->data[$column];
    }

    public function setColumn(string $column, $value): self
    {
        if (! array_key_exists($column, $this->data)) {
            throw new ModelException('Row::setAttribute() failed: Unknown column: ' . $column);
        }

        $this->data[$column] = $value;

        return $this;
    }
}