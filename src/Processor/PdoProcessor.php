<?php

namespace p810\MySQL\Processor;

use PDO;
use PDOStatement;

class PdoProcessor extends AbstractProcessor
{
    /**
     * Sets the default handlers for this processor
     * 
     * `\p810\MySQL\Processor\PdoProcessor::handleResultSet()` is used for queries that return data and is bound by
     * default to `SELECT` queries. `\p810\MySQL\Processor\PdoProcessor::handleRowCount()` is used for everything else
     * and returns the number of rows affected by the query.
     * 
     * @return void
     */
    function __construct()
    {
        $this->setHandler([$this, 'handleRowCount']);
        $this->setHandler([$this, 'handleResultSet'], 'select');
    }

    /**
     * Returns an array of associative arrays representing a result set (rows) returned from a query
     * 
     * @param \PDOStatement $statement The query's `\PDOStatement` instance
     * @return array[]
     */
    public function handleResultSet(PDOStatement $statement): array
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns an integer indicating the number of rows affected by a query
     * 
     * @param \PDOStatement $statement The query's `\PDOStatement` instance
     * @return int
     */
    public function handleRowCount(PDOStatement $statement): int
    {
        return $statement->rowCount();
    }
}
