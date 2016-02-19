<?php

namespace p810\MySQL\Model;

use \Exception;
use p810\MySQL\Connection;
use \Doctrine\Common\Inflector\Inflector;
use p810\MySQL\Relationships\Relationship;
use p810\MySQL\Helpers\Table as TableHelper;

class Row
{
    /**
     * An instance of p810\MySQL\Connection.
     *
     * @access protected
     * @var object
     */
    protected $resource;


    /**
     * A list of relationship method names.
     *
     * @access private
     * @var array
     */
    private $relationships = ['hasOne', 'belongsToOne', 'hasMany', 'belongsToMany'];


    /**
     * A flag telling whether or not to autocommit database updates.
     *
     * @access public
     * @var boolean
     */
    public $autoCommit = false;


    /**
     * Stores columns and values of the row.
     *
     * @access public
     * @var array
     */
    public $data;


    /**
     * The name of the table being represented by this row.
     *
     * @access protected
     * @var string
     */
    protected $table;


    /**
     * The primary key or a unique identifier of the table being represented by this row.
     *
     * @access protected
     * @var string
     */
    protected $uniqueKey;


    /**
     * The ID of the primary key, or the value of whichever column is used as the unique identifier for the table.
     *
     * @access protected
     * @var string|int
     */
    protected $uniqueValue;


    /**
     * Sets meta data for the object.
     *
     * @param $resource object An instance of p810\MySQL\Connection.
     * @param $table object|string An instance of p810\Model\Model or the table name as a string.
     * @param $data array Data returned by the query.
     * @return void
     */
    function __construct(Connection $resource, $table, $data)
    {
        $this->resource = $resource;

        $this->data = $data;

        if (is_object($table) && $table instanceof Model) {
            $this->table = $table->getTableName();

            $this->identifyBy($table->getPrimaryKey());
        } else {
            $this->table = TableHelper::getTableName($table);

            $primaryKey = TableHelper::getPrimaryKey($table);

            // Attempt to find the primary key so that most tables won't require a manual call to Row::identifyBy()
            if (array_key_exists($primaryKey, $data)) {
                $this->identifyBy($primaryKey);
            }
        }
    }


    /**
     * Sets the value for Row::$uniqueKey, and optionally its corresponding value in the database (Row::$uniqueValue).
     *
     * @param string $column The column to identify this row by.
     * @param string $value The value of $column, pointing to the row to select in the database.
     * @return self
     */
    public function identifyBy($column, $value = null)
    {
        $this->uniqueKey = $column;

        if (is_null($value)) {
            if (array_key_exists($this->uniqueKey, $this->data)) {
                $this->uniqueValue = $this->data[$this->uniqueKey];
            }
        } else {
            $this->uniqueValue = $value;
        }

        return $this;
    }


    /**
     * An alias for Row::identifyBy.
     *
     * @see Row::identifyBy()
     */
    public function setPrimaryKey($column, $value = null)
    {
        return $this->identifyBy($column, $value);
    }


    /**
     * Sets a value for the row and commits it to the database.
     *
     * @param $key mixed The name of the column to update.
     * @param $value mixed The value to set on the row's column.
     * @return void
     */
    public function set($key, $value, $commit = true)
    {
        if (!array_key_exists($key, $this->data) || !isset($this->uniqueKey)) {
            $commit = false;
        }

        $this->data[$key] = $value;

        if ($commit) {
            $this->commit(array($key => $value));
        }
    }


    /**
     * Gets a value from the row.
     * 
     * @param $key string|array The column name to access.
     * @return mixed
     */
    public function get($key)
    {
        if (is_array($key)) {
            $list = array();

            foreach ($key as $column) {
              $list[] = $this->get($column);
            }

            return $list;
        }

        if (!array_key_exists($key, $this->data)) {
            throw new \OutOfBoundsException;
        }

        return $this->data[$key];
    }


    /**
     * Returns a new instance of Relationship.
     *
     * @param $relationship string The name of the relationship to attempt to map.
     * @param $arguments array A variadic list of arguments. The foreign table name is required as the first, and a primary key may be supplied as the second.
     * @return boolean|array
     */
    public function relationship($relationship, ...$arguments)
    {
        if (!isset($this->uniqueValue)) {
            throw new \Exception('A relationship cannot be mapped to an unidentifiable row');
        }

        $relationship = new Relationship($this->resource, $this, $relationship, $arguments, $this->uniqueValue);

        $relationship->setLocalTable($this->table);

        return $relationship;
    }


    /**
     * Updates the database.
     *
     * @param $data array A dictionary with the column => value to set.
     * @return boolean
     */
    private function commit($data)
    {
        $query = $this->resource->update($this->table, $data)->where($this->uniqueKey, $this->uniqueValue);

        $result = $query->execute();

        if (!$result) {
            return false;
        }

        return true;
    }
}