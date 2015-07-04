<?php namespace p810\MySQL\Model;

use p810\MySQL\Connection;
use \Doctrine\Common\Inflector\Inflector;

abstract class Model
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


  /**
   * Determines the primary key of the table. A value may be set in Model::$pk to override the default, which is the table name prepended with _id.
   *
   * @return string
   */
  public function getPrimaryKey()
  {
    if(isset($this->pk)) {
      return $this->pk;
    }

    return Inflector::singularize($this->getTableName()) . '_id';
  }


  /**
   * Returns the table name. If the property Model::isPlural is not overridden, then the singular form of the classname is used.
   *
   * @return string
   */
  protected function getTableName()
  {
    if(isset($this->table)) {
      return $this->table;
    }

    return lcfirst(get_class($this));
  }
}