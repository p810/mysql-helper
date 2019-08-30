<?php

namespace p810\MySQL\Processor;

use PDO as DB;
use PDOStatement;

class Pdo extends AbstractProcessor
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
        return $statement->fetchAll(DB::FETCH_ASSOC);
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
