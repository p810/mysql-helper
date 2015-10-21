<?php

namespace p810\MySQL\Model;

use \Doctrine\Common\Inflector\Inflector;

abstract class Table
{
   /**
    * Determines the primary key of the table. A value may be set in Model::$pk to override the default, which is the table name prepended with _id.
    *
    * @return string
    */
    public function getPrimaryKey()
    {
        if (isset($this->pk)) {
            return $this->pk;
        }

        return Inflector::singularize($this->getTableName()) . '_id';
    }

   
   /**
    * Returns the table name. If the property Model::isPlural is not overridden, then the singular form of the classname is used.
    *
    * @return string
    */
    public function getTableName()
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $reflection = new \ReflectionClass($this);

        return lcfirst($reflection->getShortName());
    }
}