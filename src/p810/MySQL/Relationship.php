<?php

namespace p810\MySQL;

use Helpers\Table;

class Relationship
{
    function __construct($type, Connection $resource)
    {
        $this->type = $type;

        $this->query = $resource->query->factory->create('select');
    }

    
    public function setTables($table1, $table2)
    {
        $this->tables = [$table1, $table2];

        $this->keys = [
            Table::getPrimaryKey($table1),
            Table::getPrimaryKey($table2)
        ];
    }

    
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }


    public function setID($id)
    {
        $this->id = $id;
    }


    public function hasOne()
    {
        $result = $this->query
                    ->setTable($this->tables[1])
                    ->setColumn($this->columns)
                    ->where($this->keys[0], $this->id)
                    ->execute();

        if (count($result) === 0) {
            return false;
        }

        return $result;
    }
}