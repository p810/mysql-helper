<?php namespace p810\MySQL\Query\Statements;

use p810\MySQL\Query\Clauses\Where;
use p810\MySQL\Query\Statements\StatementContract;

class Select
extends Statement
{
  use Where;

  /**
   * Begins the query by preparing the column names.
   *
   * @param mixed $columns Column name(s) to query.
   * @param string $table The table to select data from.
   * @see StatementContract::open()
   */
  public function open($columns = '*', $table)
  {
    if(is_array($columns)) {
      foreach($columns as $column) {
        $column .= '`' . $column . '`';
      }

      $columns = implode(',', $columns);
    }

    $this->statement[] = "SELECT " . $columns . " FROM `" . $table . "`";

    return $this;
  }
}