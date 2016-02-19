<?php

namespace p810\MySQL\Query\Commands;

class Update
extends \p810\MySQL\Query\Statement
{
  use \p810\MySQL\Query\Clauses\Where;


  /**
   * {@inheritdoc}
   */
  public function begin()
  {
    $this->statement[] = "UPDATE";
  }

  
  /**
   * Sets the table for the statement.
   *
   * @param string $table The table to update.
   * @return self
   */
  public function setTable($table)
  {
    $this->table = $table;

    $this->statement[] = "`" . $table . "`";

    return $this;
  }

  
  /**
   * Sets the columns to update and their values.
   *
   * @param array $values A dictionary of columns to values to update.
   * @return self
   */
  public function setValues($values)
  {
    $this->statement[] = "SET";

    $conditions = array();

    foreach($values as $column => $value)
    {
      $conditions[] = "`" . $column . "` = " . $this->addParam($value);
    }

    $this->statement[] = implode(', ', $conditions);

    return $this;
  }

  
  /**
   * Returns the row count that was affected by the query.
   *
   * @return int
   */
  public function handleResults()
  {
    return $this->result->rowCount();
  }
}