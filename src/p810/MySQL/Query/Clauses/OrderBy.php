<?php namespace p810\MySQL\Query\Clauses;

use \OverflowException;

trait OrderBy
{
  protected $orderIterations;

  public function orderDesc($column)
  {
    $this->statement[] = $this->prefixOrderClause() . '`' . $column . '` DESC';

    $this->orderIterations++;

    return $this;
  }

  public function orderAsc($column)
  {
    $this->statement[] = $this->prefixOrderClause() . '`' . $column . '` ASC';

    $this->orderIterations++;

    return $this;
  }

  private function prefixOrderClause()
  {
    switch($this->orderIterations)
    {
      case 0:
        return 'ORDER BY ';
      break;

      case 1:
        return ', ';
      break;

      default:
        throw new OverflowException;
      break;
    }
  }
}