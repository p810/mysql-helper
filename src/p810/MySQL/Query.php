<?php namespace p810\MySQL;

use p810\MySQL\Connection;
use p810\MySQL\Query\Statements\StatementFactory;

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

    $this->factory = new StatementFactory($this->resource);
  }


  /**
   * Creates a new instance of Select.
   *
   * @param mixed $columns A list of columns, asterisk, or single column name.
   * @param string $from The table to select data from.
   * @return object An instance of p810\MySQL\Query\Statements\Select.
   */
  public function select($columns, $table)
  {
    $object = $this->factory->create('select')
              ->setColumn($columns)
              ->setTable($table);

    return $object;
  }
}