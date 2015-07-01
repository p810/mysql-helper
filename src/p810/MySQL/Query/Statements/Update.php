<?php namespace p810\MySQL\Query\Statements;

use p810\MySQL\Query\Clauses\Where;

class Update
extends Statement
{
  use Where;

  public function begin()
  {
    $this->statement[] = "UPDATE";
  }

  public function setTable($table)
  {
    $this->statement[] = "`" . $table . "`";

    return $this;
  }

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

  public function handleResults()
  {
    return $this->result->rowCount();
  }
}