<?php namespace p810\MySQL;

use p810\MySQL\Connection;
use p810\MySQL\Query\Statements\Select;

class Query
{
  /**
   * Injects an instance of Connection and grabs the PDO instance from it.
   *
   * @param object $connection An instance of p810\MySQL\Connection.
   * @return void
   */
  function __construct(Connection $connection)
  {
    $this->resource = $connection->getResource();
  }


  /**
   * Creates a new instance of Select.
   *
   * @param mixed $columns A list of columns, asterisk, or single column name.
   * @param string $from The table to select data from.
   * @return object An instance of p810\MySQL\Query\Statements\Select.
   */
  public function select($columns, $from)
  {
    $object = (new Select($this->resource))->open($columns, $from);

    return $object;
  }
}