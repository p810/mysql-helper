<?php

namespace p810\MySQL\Relationships;

use \p810\MySQL\Helpers\Table as TableHelper;

abstract class Query
{
    protected function hasOne($table, $key)
    {
        $query = $this->resource->select('*', $table);

        $query->where($key, $this->id);
        $query->limit(1);

        $result = $query->execute();

        if (count($result) > 0) {
            $result = array_shift($result);
        } else {
            $result = false;
        }

        return $result;
    }

    
    protected function hasMany($table, $key)
    {
        $query = $this->resource->select('*', $table);

        $query->where($key, $this->id);

        $result = $query->execute();

        if (count($result) === 0) {
            $result = false;
        }

        return $result;
    }


    protected function belongsToOne($table, $key)
    {
        $query = $this->resource->select('*', $table);

        $query->where($key, $this->id);
        $query->limit(1);

        $result = $query->execute();

        if (count($result) > 0) {
            $result = array_shift($result);
        } else {
            $result = false;
        }

        return $result;
    }


    protected function belongsToMany($table, $key, $intermediary)
    {
        $query = $this->resource->select('*', $intermediary);

        $query->where($key, $this->id);

        $results = $query->execute();

        if (count($results) > 0) {
            $return = array();

            foreach ($results as $result) {
                $primary_key = TableHelper::getPrimaryKey($table);

                $query = $this->resource->select('*', $table);

                $query->where($primary_key, $result[$primary_key]);

                $result = $query->execute();

                $return[] = array_shift($result);
            }

            $results = $return;
        } else {
            $results = false;
        }

        return $results;
    }
}