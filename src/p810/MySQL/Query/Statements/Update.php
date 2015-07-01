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

    foreach($values as $column => $value)
    {
      $condition = "`" . $column . "` = " . $this->addParam($value);

      if(end($values) !== $value) {
        $condition .= ", ";
      }

      $this->statement[] = $condition;
    }

    return $this;
  }

  public function handleResults()
  {
    return $this->result->rowCount();
  }
}