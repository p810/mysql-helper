<?php namespace p810\MySQL\Query\Statements;

class Insert
extends Statement
{
  public function begin()
  {
    $this->statement[] = 'INSERT INTO';
  }

  public function setTable($table)
  {
    $this->statement[] = '`' . $table . '`';

    return $this;
  }

  public function setValues($values)
  {
    $keys   = array_keys($values);
    $values = array_values($values);

    $this->setColumns($keys);

    $append = array();

    $this->statement[] = 'VALUES (';

    foreach($values as $value) {
      $append[] = "'" . $value . "'";
    }

    $this->statement[] = implode(', ', $append) . ')';

    return $this;
  }

  public function setColumns($keys)
  {
    $columns = array();

    $this->statement[] = '(';

    foreach($keys as $column) {
      $columns[] = '`' . $column . '`';
    }

    $this->statement[] = implode(', ', $columns) . ')';

    return $this;
  }

  public function handleResults()
  {
    return $this->result->rowCount();
  }
}