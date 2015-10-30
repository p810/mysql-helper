<?php namespace p810\MySQL\Query\Clauses;

trait Limit
{
    /**
     * Adds a LIMIT clause to the statement.
     *
     * @param int $total The total number of records to pull.
     * @return self
     */
    public function limit($total)
    {
        $this->statement[] = "LIMIT " . $total;

        return $this;
    }
}