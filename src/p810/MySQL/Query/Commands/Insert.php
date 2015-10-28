<?php

namespace p810\MySQL\Query\Commands;

class Insert
extends \p810\MySQL\Query\Statement
{
  /**
   * {@inheritdoc}
   */
  public function begin()
  {
    $this->statement[] = 'INSERT INTO';
  }


  /**
   * Sets the table for the statement.
   *
   * @param string $table The table to insert data into.
   * @return self
   */
  public function setTable($table)
  {
    $this->statement[] = '`' . $table . '`';

    return $this;
  }


  /**
   * Sets values for the statement.
   *
   * @param array $values A list of values to insert.
   * @return self
   */
  public function setValues($values)
  {
    $append = array();

    $this->statement[] = 'VALUES (';

    foreach($values as $value) {
      $append[] = "'" . $value . "'";
    }

    $this->statement[] = implode(', ', $append) . ')';

    return $this;
  }


  /**
   * Sets the columns for the statement.
   *
   * @param array $keys The columns to target for the table.
   * @return self
   */
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

  
  /**
   * Returns the total number of results inserted.
   *
   * @return int
   */
  public function handleResults()
  {
    return $this->result->rowCount();
  }
}