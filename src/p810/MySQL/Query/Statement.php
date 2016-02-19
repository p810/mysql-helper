<?php

namespace p810\MySQL\Query;

use \PDO;
use \PDOException;
use p810\MySQL\Connection;

abstract class Statement
{
  use \p810\MySQL\Helpers\PDOParameters;

  /**
   * The command and an optional list of clauses that will be concatenated to create the statement.
   *
   * @access protected
   * @var array
   */
  protected $statement = array();


  /**
   * A list of (common) operators that can be used in a query.
   *
   * @access protected
   * @var array
   */
  protected $operators = array(
    '=',
    '<',
    '>',
    '<=',
    '>=',
    'IS NOT NULL'
  );


  /**
   * An optional callback that specifies how a command should handle results from its queries.
   *
   * @access protected
   * @var null|callable
   */
  protected $resultHandler = null;


  /**
   * The name of the table being queried by the statement.
   *
   * @access protected
   * @var string
   */
  protected $table;


  /**
   * Injects an instance of PDO.
   *
   * @param object $connection An instance of p810\MySQL\Connection.
   * @return void
   */
  function __construct(Connection $connection)
  {
      $this->connection = $connection;

      $this->resource = $connection->getResource();

      $this->begin();
  }


  /**
   * Called at the beginning of every command.
   *
   * @return void
   */
  abstract public function begin();


  /**
   * A method that is implemented by child classes, providing a default way to handle a command's result.
   *
   * @return mixed
   */
  abstract public function handleResults();


  /**
   * If a special result handler was specified for queries of $this command, that will be called.
   * Otherwise the default result handler which $this class implements is used.
   *
   * @return mixed
   */
  private function _return($results)
  {
    if (is_callable($this->resultHandler)) {
      return call_user_func_array($this->resultHandler, [$results]);
    }

    return $this->handleResults();
  }


  /**
   * Overrides Statement::$resultHandler.
   *
   * @param $callback Callable A list mapping object to method, function name, or closure.
   * @return mixed
   */
  public function setResultHandler(Callable $callback)
  {
    $this->resultHandler = $callback;

    return $this;
  }


  /**
   * Forms a query string from Statement::$statement and returns the result.
   *
   * @return mixed
   */
  public function execute()
  {
    $statement = implode(' ', $this->statement);

    if (count($this->parameters) > 0) {
      $statement = $this->resource->prepare($statement);

      $statement->execute($this->parameters);

      $this->result = $statement;
    } else {
      $this->result = $this->resource->query($statement);
    }

    return $this->_return($this->result);
  }


  /**
   * An alias for Statement::execute().
   *
   * @see Statement::execute()
   */
  public function get()
  {
    return $this->execute();
  }
}