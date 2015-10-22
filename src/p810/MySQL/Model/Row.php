<?php

namespace p810\MySQL\Model;

use PDO;
use Exception;
use OutOfBoundsException;
use p810\MySQL\Connection;
use p810\MySQL\Relationship;
use \Doctrine\Common\Inflector\Inflector;
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

    $this->relationship = new Relationship($this, $this->id);
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
   * {@todo}
   */
  public function hasOne($table, $foreign_key = null)
  {
    $table = Inflector::pluralize($table);

    if (is_null($foreign_key)) {
      $foreign_key = $this->model->getPrimaryKey( $this->model->getTableName() );
    }

    $result = $this->relationship->hasOne($table, $foreign_key);

    if ($result) {
      $table = Inflector::singularize($table);

      $this->data[$table] = $result;
    }

    return $result;
  }


  /**
   * {@todo}
   */
  public function hasMany($table, $foreign_key = null)
  {
    if (is_null($foreign_key)) {
      $foreign_key = $this->model->getPrimaryKey();
    }

    $results = $this->relationship->hasMany($table, $foreign_key);

    if ($results) {
      $this->data[$table] = array();

      foreach($results as $result) {
        $this->data[$table][] = $result;
      }
    }

    return $results;
  }


  /**
   * {@todo}
   */
  public function belongsToOne($table, $foreign_key = null)
  {
    $table = Inflector::pluralize($table);

    if (is_null($foreign_key)) {
      $foreign_key = TableHelper::getPrimaryKey($table);
    }

    $result = $this->relationship->belongsToOne($table, $foreign_key);

    if ($result) {
      $table = Inflector::singularize($table);

      $this->data[$table] = $result;
    }

    return $result;
  }


  /**
   * {@todo}
   */
  public function belongsToMany($table, $foreign_key = null)
  {
    $intermediary = $this->model->getTableName() . '_to_' . $table;

    if (is_null($foreign_key)) {
      $foreign_key = $this->model->getPrimaryKey();
    }

    $results = $this->relationship->belongsToMany($intermediary, $table, $foreign_key);

    if ($results) {
      $this->data[$table] = array();

      foreach ($results as $result) {
        $this->data[$table][] = $result;
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