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

    $this->model->getRelatedData($this->id, $this->data);
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
   * Gets a value from the row.
   * 
   * @param string $key The column name to access.
   * @return mixed
   */
  public function get($key)
  {
    if (!array_key_exists($key, $this->data)) {
      throw new OutOfBoundsException;
    }

    return $this->data[$key];
  }


  /**
   * Provides access to columns in the row like they are properties of the class.
   *
   * @param string $key The column name to access.
   * @return mixed
   */
  function __get($key)
  {
    return $this->get($key);
  }


  /**
   * Fetches foreign rows that this row owns. Relationships are mapped by the primary key of this row's table.
   *
   * @param $table The foreign table to pull data from.
   * @param $key If supplied, the associated key between the two tables.
   * @return mixed
   */
  public function has($table, $key = null)
  {
    if (is_null($key)) {
      $key = substr($table, 0, strlen($table) - 1);

      $key .= '_id';
    }

    $rows = $this->model->resource->select('*', $table)
              ->where($key, $this->data[$key])
              ->execute();

    if (count($rows) === 0) {
      return false;
    }

    foreach($rows as &$row) {
      unset($row[$key]);
    }

    if (count($rows) === 1) {
      $rows = array_shift($rows);
    }

    $this->data[$table] = $rows;

    return $rows;
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