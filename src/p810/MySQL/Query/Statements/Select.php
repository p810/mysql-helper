<?php namespace p810\MySQL\Query\Statements;

use p810\MySQL\Query\Clauses\Where;
use p810\MySQL\Query\Statements\StatementContract;

class Select
extends Statement
{
  use Where;

  
  /**
   * {@inheritdoc}
   */
  public function begin()
  {
    $this->statement[] = 'SELECT';
  }


  /**
   * Sets the column(s) for the query statement.
   *
   * @param mixed $columns Column name(s) to query.
   * @return self
   */
  public function setColumn($columns = '*')
  {
    if(is_array($columns)) {
      foreach($columns as $column) {
        $column .= '`' . $column . '`';
      }

      $columns = implode(',', $columns);
    }

    $this->statement[] = $columns;

    return $this;
  }


  /**
   * Sets the table for the query statement.
   *
   * @param string $table The table to select data from.
   * @return self
   */
  public function setTable($table)
  {
    $this->statement[] = "FROM " . $table;

    return $this;
  }
}