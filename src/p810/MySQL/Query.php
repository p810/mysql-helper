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


  /**
   * Creates a new instance of Update.
   *
   * @param string $table The table to update.
   * @param mixed $columns The columns to update and their corresponding values.
   * @return object An instance of p810\MySQL\Query\Statements\Update.
   */
  public function update($table, $values)
  {
    $object = $this->factory->create('update')
              ->setTable($table)
              ->setValues($values);
              
    return $object;
  }


  /**
   * Creates a new instance of Insert.
   *
   * @param string $table The table to insert into.
   * @param array $values Either a list or dictionary of values to insert. Use a dictionary to specify columns.
   * @return object An instance of p810\MySQL\Query\Statements\Insert.
   */
  public function insert($table, $values)
  {
    $object = $this->factory->create('insert')
              ->setTable($table)
              ->setValues($values);

    return $object;
  }
}