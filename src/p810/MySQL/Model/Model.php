<?php namespace p810\MySQL\Model;

use p810\MySQL\Connection;
use Exception;

abstract class Model
{
  /**
   * The table being represented by the model.
   *
   * @access protected
   * @var string
   */
  public $table;


  function __construct(Connection $resource)
  {
    $this->resource = $resource;
  }


  public function find($id)
  {
    $data = $this->resource->select('*', $this->table)
                           ->where($this->getPrimaryKey(), $id)
                           ->execute();

    if(!$data) {
      throw new Exception;
    }

    return $this->createRowModel(array_shift($data));
  }


  public function getPrimaryKey()
  {
    if(isset($this->pk)) {
      return $this->pk;
    }

    return $this->table . '_id';
  }


  protected function createRowModel($data)
  {
    return new Row($this, $data);
  }
}