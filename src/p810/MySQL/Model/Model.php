<?php namespace p810\MySQL\Model;

use p810\MySQL\Connection;
use \Doctrine\Common\Inflector\Inflector;

abstract class Model
extends Table
{
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