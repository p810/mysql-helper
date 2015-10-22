<?php

namespace p810\MySQL;

use p810\MySQL\Model\Row;
use p810\MySQL\Helpers\Table as TableHelper;

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


    public function hasMany($table, $key)
    {
        $query = $this->row->model->resource->select('*', $table);

        $query->where($key, $this->id);

        return $query->execute();
    }


    public function belongsToOne($table, $key)
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


    public function belongsToMany($intermediary, $table, $key)
    {
        $query = $this->row->model->resource->select('*', $intermediary);

        $query->where($key, $this->id);

        $results = $query->execute();

        if (is_array($results) && count($results) > 0) {
            $return = array();

            foreach ($results as $result) {
                $query = $this->row->model->resource->select('*', $table);

                $primary_key = TableHelper::getPrimaryKey($table);

                $query->where($primary_key, $result[$primary_key]);

                $result = $query->execute();

                $return[] = array_shift($result);
            }

            $results = $return;
        }

        return $results;
    }
}