<?php

namespace p810\MySQL\Processor;

use PDO;
use PDOStatement;

class PdoProcessor extends AbstractProcessor
{
    function __construct()
    {
        $this->setHandler([$this, 'handleRowCount']);
        $this->setHandler([$this, 'handleResultSet'], 'select');
    }

    /**
     * Returns an array of associative arrays representing a result set (rows) returned from a query
     * 
     * @param \PDOStatement $statement
     * @return array
     */
    public function handleResultSet(PDOStatement $statement): array
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns an integer indicating the number of rows affected by a query
     * 
     * @param \PDOStatement $statement
     * @return int
     */
    public function handleRowCount(PDOStatement $statement): int
    {
        return $statement->rowCount();
    }
}
