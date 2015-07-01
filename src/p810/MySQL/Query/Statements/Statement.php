<?php namespace p810\MySQL\Query\Statements;

use \PDO;
use \PDOException;

abstract class Statement
{
  /**
   * The base statement and optional list of clauses that will be concatenated to create the statement.
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
   * Injects an instance of PDO.
   *
   * @param object $resource The instance of PDO to inject (returned from Connection::getResource()).
   * @return void
   */
  function __construct(PDO $resource)
  {
    $this->resource = $resource;

    $this->begin();
  }


  /**
   * Begins the statement. This is a method that must be implemented by child classes.
   *
   * @return void
   */
  abstract public function begin();


  /**
   * Creates the full statement and returns either false (if the query did not succeed) or an instance of PDOStatement.
   *
   * @return mixed
   */
  public function execute()
  {
    $statement = implode(' ', $this->statement);

    return $this->resource->query($statement);
  }
}