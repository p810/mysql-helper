<?php

namespace p810\MySQL\Relationships;

use p810\MySQL\Connection;
use \Doctrine\Common\Inflector\Inflector;
use p810\MySQL\Helpers\Table as TableHelper;

class Relationship
extends Query
{
    private $relationships = ['hasOne', 'hasMany', 'belongsToOne', 'belongsToMany'];

    
    function __construct(Connection $resource, $id)
    {
        $this->resource = $resource;

        $this->id = $id;
    }


    public function setLocalTable($local)
    {
        $this->local = Inflector::pluralize($local);
    }


    function __call($method, $arguments)
    {
        if (!method_exists($this, $method) || !in_array($method, $this->relationships)) {
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