<?php

namespace p810\MySQL;

use p810\MySQL\Model\Row;
use p810\MySQL\Helpers\Table;

class Relationship
{
    function __construct(Row $row, $id)
    {
        $this->row = $row;
        $this->id = $id;
    }

    
    public function hasOne($table, $key)
    {
        $query = $this->row->model->resource->select('*', $table);

        $query->where($key, $this->id);
        $query->limit(1);

        $result = $query->execute();

        if (is_array($result) && count($result) > 0) {
            $result = array_shift($result);
        }

        return $result;
    }
}