<?php namespace p810\MySQL\Query\Statements;

use \PDO;
use p810\MySQL\Query\Clauses\Where;
use p810\MySQL\Query\Clauses\OrderBy;

class Select
extends Statement
{
  use Where;
  use OrderBy;

  
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


  /**
   * Returns the resultset as an associative array.
   *
   * @return array
   */
  public function handleResults()
  {
    return $this->result->fetchAll(PDO::FETCH_ASSOC);
  }
}