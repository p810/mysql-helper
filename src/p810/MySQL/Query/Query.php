<?php namespace p810\MySQL\Query;

use p810\MySQL\Connection;
use p810\MySQL\Query\Statements\Select;

class QueryBuilder
{
  use Select;

  function __construct(Connection $connection)
  {
    $this->resource = $connection->getResource();
  }
}