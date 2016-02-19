<?php

namespace p810\MySQL\Factories;

use \PDO;
use p810\MySQL\Connection;

class Statement
{
  /**
   * Locally stores a reference to the connection and the PDO resource.
   *
   * @param object $resource An instance of p810\MySQL\Connection.
   * @return void
   */
  function __construct(Connection $connection)
  {
    $this->connection = $connection;

    $this->resource = $connection->getResource();
  }


  /**
   * Creates an instance of the statement to be executed.
   *
   * @param string $type The type of statement.
   * @return object
   */
  public function create($type)
  {
    $class = 'p810\\MySQL\\Query\\Commands\\' . ucfirst($type);

    return new $class($this->connection, $this->resource);
  }
}