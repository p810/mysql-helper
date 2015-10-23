<?php

namespace p810\MySQL\Relationships;

use p810\MySQL\Connection;
use \Doctrine\Common\Inflector\Inflector;
use p810\MySQL\Helpers\Table as TableHelper;

class Relationship
extends Query
{
    /**
     * Injects an instance of p810\MySQL\Connection and the ID of the model this relationship correlates.
     *
     * @param $resource object An instance of p810\MySQL\Connection.
     * @param $id int The model's primary ID.
     * @return void
     */    
    function __construct(Connection $resource, $id)
    {
        $this->resource = $resource;

        $this->id = $id;
    }


    /**
     * Sets the local table of this relationship. 
     *
     * @param $local string The name of the local table.
     * @return void
     */
    public function setLocalTable($local)
    {
        $this->local = Inflector::pluralize($local);
    }


    /**
     * Calls a method belonging to Query and returns its result.
     *
     * @param $method string The name of the method to call.
     * @param $arguments array A variadic list of arguments.
     * @return bool|array
     */
    function __call($method, $arguments)
    {
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException;
        }

        $table = $arguments[0];

        if (isset($arguments[1])) {
            $foreign_key = $arguments[1];
        } else {
            $foreign_key = null;
        }

        $table = Inflector::pluralize($table);

        if ($method == 'belongsToMany') {
            $bridge = $this->local . '_to_' . $table;
        } else {
            $bridge = null;
        }

        if (is_null($foreign_key)) {
            switch ($method) {
                case 'hasOne':
                case 'hasMany':
                case 'belongsToMany':
                    $foreign_key = TableHelper::getPrimaryKey($this->local);
                break;

                case 'belongsToOne':
                    $foreign_key = TableHelper::getPrimaryKey($table);
                break;
            }
        }

        $arguments = [$table, $foreign_key, $bridge];

        if (is_null($bridge)) {
            unset($arguments[2]);
        }

        return call_user_func_array([$this, $method], $arguments);
    }
}