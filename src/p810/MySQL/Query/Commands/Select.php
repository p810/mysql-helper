<?php

namespace p810\MySQL\Query\Commands;

use \PDO;
use p810\MySQL\Model\Row;

class Select
extends \p810\MySQL\Query\Statement
{
  use \p810\MySQL\Query\Clauses\Where;
  use \p810\MySQL\Query\Clauses\OrderBy;
  use \p810\MySQL\Query\Clauses\Limit;

  
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
    $this->table = $table;

    $this->statement[] = "FROM " . $table;

    return $this;
  }


  /**
   * Returns the result set as a list of instances of p810\MySQL\Model\Row.
   *
   * @return array
   */
  public function handleResults()
  {
      $results = $this->result->fetchAll(PDO::FETCH_ASSOC);

      $rows = array();

      foreach ($results as $result) {
          $rows[] = new Row($this->connection, $this->table, $result);
      }

      return $rows;
  }
}