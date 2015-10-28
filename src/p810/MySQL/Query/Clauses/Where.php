<?php

namespace p810\MySQL\Query\Clauses;

trait Where
{
  /**
   * Provides access to Where::_and and Where::_or through non-prefixed names.
   *
   * This is done because both are words reserved to the compiler.
   *
   * @param $method string The name of the method being called.
   * @param $arguments null|array An optional list of arguments passed to the call.
   * @return self
   */
  function __call($method, $arguments)
  {
    if ($method == 'and') {
      $result = call_user_func_array([$this, '_and'], $arguments);
    } elseif ($method == 'or') {
      $result = call_user_func_array([$this, '_or'], $arguments);
    }

    return $result;
  }


  /**
   * Appends a where clause.
   *
   * @param string $column The name of the column to look up.
   * @param string $operator (Optional) The operator to use.
   * @param mixed $value The value that the column should be compared against.
   * @return self
   */
  public function where(...$arguments)
  {
    $clause = array();

    $clause[] = "WHERE";

    $this->buildQueryString($clause, ...$arguments);

    $this->statement[] = implode(' ', $clause);

    return $this;
  }


  /**
   * Appends a combining "and" operator to a where clause.
   *
   * @param string $column The name of the column to look up.
   * @param string $operator (Optional) The operator to use.
   * @param mixed $value The value that the column should be compared against.
   * @return self
   */
  protected function _and(...$arguments)
  {
    $clause = array();

    $clause[] = "AND";

    $this->buildQueryString($clause, ...$arguments);

    $this->statement[] = implode(' ', $clause);

    return $this;
  }


  /**
   * Appends a combining "or" operator to a where clause.
   * 
   * @param string $column The name of the column to look up.
   * @param string $operator (Optional) The operator to use.
   * @param mixed $value The value that the column should be compared against.
   * @return self
   */
  protected function _or(...$arguments)
  {
    $clause = array();

    $clause[] = "OR";

    $this->buildqueryString($clause, ...$arguments);

    $this->statement[] = implode(' ', $clause);

    return $this;
  }


  /**
   * Takes the variadic list of arguments from Where::where(), Where::and(), and Where::or() and creates a valid
   * SQL statement from it.
   *
   * The result is appended to the array passed in as the first argument as it is passed by reference.
   *
   * @param array $clause A list of "tokens" to be joined for the SQL statement.
   * @param array $arguments Whichever list was passed in from the above three methods.
   * @return void
   */
  private function buildQueryString(&$clause, ...$arguments)
  {
    switch (count($arguments))
    {
      case 2:
        $clause[] = "`" . $arguments[0] . "` = " . $this->addParam($arguments[1]);
      break;

      case 3:
        if(!in_array($arguments[1], $this->operators)) {
          throw new InvalidArgumentException;
        }

        $clause[] = "`" . $arguments[0] . '` ' . $arguments[1] . " " . $this->addParam($arguments[2]);
      break;

      default:
        throw new InvalidArgumentException;
      break;
    }
  }
}