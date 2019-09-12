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
     * @param \PDOStatement $statement
     * @return array
     */
    public function handleResultSet(PDOStatement $statement): array
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param \PDOStatement $statement
     * @return int
     */
    public function handleRowCount(PDOStatement $statement): int
    {
        return $statement->rowCount();
    }
}
