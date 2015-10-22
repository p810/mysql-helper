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
}