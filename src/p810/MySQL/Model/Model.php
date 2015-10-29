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
        $data = $this->resource->select('*', $this->getTableName())
                               ->where($this->getPrimaryKey(), $id)
                               ->execute();

        return $this->resultToRow($data);
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
      return $this->resource->select('*', $this->getTableName())
              ->setResultHandler([$this, 'resultToRow'])
              ->where(...$arguments);
    }


    /**
     * Handles PDO resultsets, returning a list of rows, a single row, or false if the query returned nothing.
     *
     * @param $results object A resultset from a PDO query.
     * @return mixed
     */
    public function resultToRow($results)
    {
        $results = $results->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 1) {
          $rows = array();

          foreach ($results as $data) {
            $rows[] = new Row($this, $data);
          }

          return $rows;
        } elseif (count($results) === 1) {
          return new Row($this, array_shift($results));
        } elseif (count($results) === 0 || !$results) {
          return false;
        }
    }
}