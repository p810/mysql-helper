<?php namespace p810\MySQL\Model;

use p810\MySQL\Connection;
use PDO;
use Exception;
use OutOfBoundsException;

class Row
{
  /**
   * The ID of the row.
   *
   * @access protected
   * @var int
   */
  protected $id;


  /**
   * Injects an instance of p810\Model\Model, sets the row's data, and determines its ID.
   *
   * @param object $model An instance of p810\Model\Model.
   * @param array $data The data returned by Model::find()
   * @return void
   */
  function __construct(Model $model, $data)
  {
    $this->model    = $model;
    $this->data     = $data;
    $this->id       = $data[$model->getPrimaryKey()];
  }


  /**
   * Sets a value for the row and commits it to the database.
   *
   * @param mixed $key The name of the column to update.
   * @param mixed $value The value to set on the row's column.
   * @return void
   */
  public function set($key, $value)
  {
    if(!array_key_exists($key, $this->data)) {
      throw new OutOfBoundsException;
    }

    $this->data[$key] = $value;

    $this->commit(array($key => $value));
  }


  /**
   * Provides access to columns in the row like they are properties of the class.
   *
   * @param string $key The column name to access.
   * @return mixed
   */
  function __get($key)
  {
    if(!array_key_exists($key, $this->data)) {
      throw new OutOfBoundsException;
    }

    return $this->data[$key];
  }


  /**
   * Updates the database.
   *
   * @param array $data A dictionary with the column => value to set.
   * @return bool
   */
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