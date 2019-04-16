<?php

namespace p810\MySQL;

use PDO;

/**
 * Represents a connection to the database and wraps an instance of \PDO.
 */
interface ConnectionInterface
{
    public function getPdo(): PDO;

    /**
     * @throws \PDOException
     * @return false|\PDOStatement
     */
    public function prepare(string $query);

    public function select(): Query;

    public function insert(?array $columnsToValues = null): Query;

    public function update(): Query;

    public function delete(): Query;
}