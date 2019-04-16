<?php

namespace p810\MySQL;

use PDO;

/**
 * Represents a connection to the database and wraps an instance of \PDO.
 */
interface ConnectionInterface
{
    /**
     * Returns the connection's \PDO instance
     * 
     * @return \PDO
     */
    public function getPdo(): PDO;

    /**
     * Proxies the provided $query into \PDO::prepare()
     * 
     * @throws \PDOException
     * @return false|\PDOStatement
     */
    public function prepare(string $query);

    /**
     * Returns an instance of \p810\MySQL\Query with a Select builder
     * 
     * @return \p810\MySQL\Query
     */
    public function select(): Query;

    /**
     * Returns an instance of \p810\MySQL\Query with an Insert builder
     * 
     * @param array|null $columnsToValues A column => value array to pass to the builder
     * @return \p810\MySQL\Query
     */
    public function insert(?array $columnsToValues = null): Query;

    /**
     * Returns an instance of \p810\MySQL\Query with an Update builder
     * 
     * @return \p810\MySQL\Query
     */
    public function update(): Query;

    /**
     * Returns an instance of \p810\MySQL\Query with a Delete builder
     * 
     * @return \p810\MySQL\Query
     */
    public function delete(): Query;
}