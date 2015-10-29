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

        if(!$data) {
          return false;
        }

        $data = array_shift($data);

        return new Row($this, $data);
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
      $handler = function ($results) {
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
      };

      return $this->resource->select('*', $this->getTableName())
              ->setResultHandler($handler)
              ->where(...$arguments);
    }
}