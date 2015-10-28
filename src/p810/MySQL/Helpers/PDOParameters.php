<?php

namespace p810\MySQL\Helpers;

trait PDOParameters
{
  /**
   * Stores a list of parameters to be bound to the query.
   *
   * @access protected
   * @var array
   */
  protected $parameters = array();


  /**
   * Adds a parameter to the list and returns its placeholder value.
   *
   * @param mixed $value The parameter to add to the query.
   * @return string
   */
  public function addParam($value)
  {
    $this->parameters[] = $value;

    return '?';
  }
}