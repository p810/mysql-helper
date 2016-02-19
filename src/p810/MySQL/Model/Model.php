<?php

namespace p810\MySQL\Model;

abstract class Model
extends Table
{
   /**
    * Fetches a row by its ID.
    *
    * @param int $id The ID of the row to be queried.
    * @return mixed
    */
    public function find($id)
    {
        return $this->resource->select('*', $this->getTableName())->where($this->getPrimaryKey(), $id)->execute();
    }


    /**
     * Creates a select statement for the table represented by the model and appends a where clause.
     *
     * The returned value is an instance of Select.
     *
     * @param $arguments array A variadic list of arguments.
     * @return object
     */
    public function where(...$arguments)
    {
        return $this->resource->select('*', $this->getTableName())->where(...$arguments);
    }
}