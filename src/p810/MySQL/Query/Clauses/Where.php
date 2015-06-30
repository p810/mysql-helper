<?php namespace p810\MySQL\Query\Clauses;

trait Where
{
  /**
   * Appends a simple where clause, asserting that a single column name is equal to the $value.
   *
   * @param string $column The name of the column to look up.
   * @param mixed $value The value that the column should equal.
   * @return parent
   */
  public function whereEquals($column, $value)
  {
    $this->statement[] = "WHERE `" . $column . "` = '" . $value . "'";

    return $this;
  }
}