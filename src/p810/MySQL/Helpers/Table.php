<?php

namespace p810\MySQL\Helpers;
use \Doctrine\Common\Inflector\Inflector;

class Table
{
    /**
     * Resolves a table's name, following the plural naming scheme.
     *
     * @param $class string|object An instance of p810\Model\Model or a string.
     * @return string
     */
    public static function getTableName($class)
    {
        if (is_object($class)) {
            $reflection = new \ReflectionClass($class);

            $class = $reflection->getShortName();
        }

        return lcfirst(Inflector::pluralize($class));
    }


    /**
     * Resolves a table's primary key, following the naming scheme where the table's singular name is prepended with _id.
     *
     * @param $table string The name of the table.
     * @return string
     */
    public static function getPrimaryKey($table)
    {
        return Inflector::singularize($table) . '_id';
    }
}