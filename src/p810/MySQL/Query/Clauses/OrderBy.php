<?php namespace p810\MySQL\Query\Clauses;

use \OverflowException;

trait OrderBy
{
  /**
   * Counts how many times this clause has been called.
   *
   * @access protected
   * @var int
   */
  protected $orderIterations;


  /**
   * Adds an ORDER BY clause set to DESC value.
   *
   * @param string $column The name of the column to order by.
   * @return self
   */
  public function orderDesc($column)
  {
    $this->statement[] = $this->prefixOrderClause() . '`' . $column . '` DESC';

    $this->orderIterations++;

    return $this;
  }

  
  /**
   * Adds an ORDER BY clause set to ASC value.
   *
   * @param string $column The name of the column to order by.
   * @return self
   */
  public function orderAsc($column)
  {
    $this->statement[] = $this->prefixOrderClause() . '`' . $column . '` ASC';

    $this->orderIterations++;

    return $this;
  }

  
  /**
   * Returns the prefix for the query; "ORDER BY," if this is the first iteration, or a comma if it's the second.
   *
   * Since two iterations is the limit (one ASC, one DESC) an exception is thrown for subsequent attempts.
   *
   * @return string
   * @throws OverflowException if no more ORDER BY clauses can be appended to the statement.
   */
  private function prefixOrderClause()
  {
    switch($this->orderIterations)
    {
      case 0:
        return 'ORDER BY ';
      break;

      case 1:
        return ', ';
      break;

      default:
        throw new OverflowException;
      break;
    }
  }
}