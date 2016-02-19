<?php

namespace p810\MySQL\Query\Commands;

class Delete
extends \p810\MySQL\Query\Statement
{
    use \p810\MySQL\Query\Clauses\Where;

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        $this->statement[] = "DELETE";
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

        $this->statement[] = 'FROM `' . $table . '`';

        return $this;
    }


    /**
     * {@todo}
     */
    public function handleResults()
    {
        return $this->result->rowCount();
    }
}