<?php

namespace p810\MySQL\Model;

use p810\MySQL\Query;
use p810\MySQL\Model;

class Row {
    function __construct(array $data, Model $model) {
        $this->data  = $data;
        $this->model = $model;
    }

    public function save(): bool {
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
}