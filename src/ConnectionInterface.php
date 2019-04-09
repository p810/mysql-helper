<?php

namespace p810\MySQL;

use PDO;

/**
 * Represents a connection to the database and wraps an instance of \PDO.
 */
interface ConnectionInterface
{
    /**
     * @return \PDO
     */
    public function getPdo(): PDO;

    /**
     * @param string|array $columns
     * @return \p810\MySQL\Query
     */
    public function select($columns = '*'): Query;

    /**
     * @param array|null $columnsToValues
     * @return \p810\MySQL\Query
     */
    public function insert(?array $columnsToValues = null): Query;

    /**
     * @param string $table
     * @return \p810\MySQL\Query
     */
    public function update(string $table): Query;

    /**
     * @return \p810\MySQL\Query
     */
    public function delete(): Query;
}