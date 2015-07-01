<?php namespace p810\MySQL\Query\Clauses;

trait Where
{
  /**
   * Counts how many times this clause has been called.
   *
   * @access protected
   * @var int
   */
  protected $whereIterations;


  /**
   * Appends a where clause.
   *
   * This method accepts variadic input, and retrieves arguments through func_get_args().
   *
   * @param string $column The name of the column to look up.
   * @param string $operator (Optional) The operator to use.
   * @param mixed $value The value that the column should equal.
   * @return parent
   */
  public function where()
  {
    $arguments = func_get_args();

    $clause = array();

    if($this->whereIterations > 0) {
      $clause[] = "AND";
    } else {
      $clause[] = "WHERE";
    }

    switch(count($arguments))
    {
      case 2:
        $clause[] = "`" . $arguments[0] . "` = '" . $arguments[1] . "'";
      break;

      case 3:
        if(!in_array($arguments[1], $this->operators)) {
          throw new InvalidArgumentException;
        }

        $clause[] = "`" . $arguments[0] . '` ' . $arguments[1] . " '" . $arguments[2] . "'";
      break;

      default:
        throw new InvalidArgumentException;
      break;
    }

    $this->statement[] = implode(' ', $clause);

    $this->whereIterations++;

    return $this;
  }
}