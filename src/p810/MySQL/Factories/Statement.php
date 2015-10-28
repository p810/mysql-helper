<?php

namespace p810\MySQL\Factories;

use \PDO;

class Statement
{
  /**
   * Injects an instance of PDO to the object.
   *
   * @param object $resource An instance of PDO.
   * @return void
   */
  function __construct(PDO $resource)
  {
    $this->resource = $resource;
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

    return new $class($this->resource);
  }
}