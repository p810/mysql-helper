<?php

namespace p810\MySQL\Relationships;

use p810\MySQL\Connection;
use p810\MySQL\Model\Row;
use \Doctrine\Common\Inflector\Inflector;
use p810\MySQL\Helpers\Table as TableHelper;

class Relationship
extends Query
{
    /**
     * Injects an instance of p810\MySQL\Connection and the ID of the model this relationship correlates.
     *
     * @param $resource object An instance of p810\MySQL\Connection.
     * @param $model object An instance of p810\MySQL\Model\Model.
     * @param $method string The type of relationship being queried.
     * @param $arguments array|null A variadic list of arguments.
     * @param $id int The model's primary ID.
     * @return void
     */    
    function __construct(Connection $resource, Row $row, $method, $arguments, $id)
    {
        $this->resource = $resource;

        $this->row = $row;

        $this->method = $method;

        $this->arguments = $arguments;

        $this->id = $id;

        $this->columns = '*';
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
     * Sets the columns to retrieve from the foreign table.
     *
     * @param $columns string|array A string or array of columns to select.
     * @return void
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }


    /**
     * Calls a method belonging to Query and returns its result.
     *
     * @param $method string The name of the method to call.
     * @param $arguments array A variadic list of arguments.
     * @return bool|array
     */
    public function map()
    {
        if (!method_exists($this, $this->method)) {
            throw new \BadMethodCallException;
        }

        $table = $this->arguments[0];

        if (isset($this->arguments[1])) {
            $foreign_key = $this->arguments[1];
        } else {
            $foreign_key = null;
        }

        $table = Inflector::pluralize($table);

        if ($this->method == 'belongsToMany') {
            $bridge = $this->local . '_to_' . $table;
        } else {
            $bridge = null;
        }

        if (is_null($foreign_key)) {
            switch ($this->method) {
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

        $results = call_user_func_array([$this, $this->method], $arguments);

        $this->updateModel($results);

        return $results;
    }


    /**
     * Updates the model represented by this Relationship with new data.
     *
     * @param $results array|boolean The result of the query.
     * @return void
     */
    private function updateModel($results)
    {
        if (!$results || count($results) === 0) {
            return;
        }

        switch ($this->method) {
            case 'hasOne':
            case 'belongsToOne':
                $this->row->set($this->arguments[0], $results, false);
            break;

            case 'hasMany':
            case 'belongsToMany':
                $set = array();

                foreach ($results as $result) {
                    $set[] = $result;
                }

                $this->row->set($this->arguments[0], $set, false);
            break;
        }
    }
}