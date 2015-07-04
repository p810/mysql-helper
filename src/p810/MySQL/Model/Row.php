<?php namespace p810\MySQL\Model;

use p810\MySQL\Connection;
use PDO;
use Exception;
use OutOfBoundsException;

class Row
{
  protected $id;


  function __construct(Model $model, $data)
  {
    $this->model    = $model;
    $this->data     = $data;
    $this->id       = $data[$model->getPrimaryKey()];
  }


  public function set($key, $value)
  {
    if(!array_key_exists($key, $this->data)) {
      throw new OutOfBoundsException;
    }

    $this->data[$key] = $value;

    $this->commit(array($key => $value));
  }


  function __get($key)
  {
    if(!array_key_exists($key, $this->data)) {
      throw new OutOfBoundsException;
    }

    return $this->data[$key];
  }


  private function commit($data)
  {
    $query = $this->model->resource->update($this->model->table, $data)
                                   ->where($this->model->getPrimaryKey(), $this->id);

    $result = $query->execute();

    if(!$result) {
      return false;
    }

    return true;
  }
}