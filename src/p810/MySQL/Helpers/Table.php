<?php

namespace p810\MySQL\Helpers;
use \Doctrine\Common\Inflector\Inflector;

class Table
{
    public static function getTableName($class)
    {
        if (is_object($class)) {
            $reflection = new \ReflectionClass($class);

            $class = $reflection->getShortName();
        }

        return lcfirst($class);
    }


    public static function getPrimaryKey($table)
    {
        return Inflector::singularize($table) . '_id';
    }
}