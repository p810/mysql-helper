<?php

namespace p810\MySQL\Model;

use p810\MySQL\Connection;
use \Doctrine\Common\Inflector\Inflector;
use p810\MySQL\Relationships\Relationship;
use p810\MySQL\Helpers\Table as TableHelper;

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
   * Possible relationship method names.
   *
   * @access private
   * @var array
   */
  private $relationships = ['hasOne', 'belongsToOne', 'hasMany', 'belongsToMany'];


  /**
   * Injects an instance of p810\Model\Model, sets the row's data, and determines its ID.
   *
   * @param object $model An instance of p810\Model\Model.
   * @param array $data The data returned by Model::find()
   * @return void
   */
  function __construct(Model $model, $data)
  {
    $this->model = $model;
    $this->data  = $data;
    $this->id    = $data[$model->getPrimaryKey()];

    $this->relationship = new Relationship($this->model->resource, $this->id);

    $this->relationship->setLocalTable($this->model->getTableName());
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
      throw new \OutOfBoundsException;
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
      throw new \OutOfBoundsException;
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


  public function relationship($relationship, ...$arguments)
  {
    $results = call_user_func_array([$this->relationship, $relationship], $arguments);

    switch ($relationship) {
      case 'hasOne':
      case 'belongsToOne':
        $this->data[$arguments[0]] = $results;
      break;

      case 'hasMany':
      case 'belongsToMany':
        $this->data[$arguments[0]] = array();

        foreach ($results as $result) {
          $this->data[$arguments[0]][] = $result;
        }
    }

    return $results;
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