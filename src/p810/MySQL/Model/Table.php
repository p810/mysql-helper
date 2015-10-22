<?php

namespace p810\MySQL\Model;

use \p810\MySQL\Connection;
use \p810\MySQL\Helpers\Table as TableHelper;

abstract class Table
{
    protected $relationships = ['hasOne', 'hasMany', 'belongsToOne', 'belongsToMany'];

   /**
    * Injects an instance of p810\MySQL\Connection.
    *
    * @param object $resource An instance of p810\MySQL\Connection.
    * @return void
    */
    function __construct(Connection $resource)
    {
        $this->resource = $resource;
    }

    
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

        return TableHelper::getPrimaryKey($this->getTableName());
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

        return TableHelper::getTableName($this);
    }


    /**
     * @todo: write documentation
     */
    protected function getRelatedData($id, &$data)
    {
        foreach ($this->relationships as $relationship) {
            if (property_exists($this, $relationship)) {
                foreach ($this->$relationship as $table => $columns) {
                    if (is_numeric($table)) {
                        $table = $columns;
                        
                        $columns = '*';
                    } else {
                        if (is_array($columns)) {
                            $columns = implode(',', $table);
                        }
                    }

                    $method = $relationship;

                    $relationship = new Relationship($this->resource);

                    $relationship->setTables($this->getTableName(), $table);
                    $relationship->setColumns($columns);
                    $relationship->setID($id);

                    $data[$table] = call_user_func_array([$relationship, $method], [$this->resource]);

                    unset($relationship);
                }
            }
        }
    }
}