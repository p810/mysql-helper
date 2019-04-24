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
     * Specifies a default query processor for all queries, or the given query type
     * 
     * @param callable $processor The callback to use when the query is executed
     * @param string   $type      An optional query type for which this processor should be used
     * @return void
     */
    public function setDefaultProcessor(callable $processor, string $type = '*'): void;

    /**
     * Executes the given query and returns a \PDOStatement
     * 
     * @param string $query
     * @param array  $input
     * @return bool|\PDOStatement
     */
    public function raw(string $query, array $input = []);

    /**
     * Returns an instance of \p810\MySQL\Query with a Select builder
     * 
     * @param string|array|null $columns Which columns to include in the query's results.
     *                                   Accepts either a numeric or associative array; in
     *                                   the latter case, the keys are used as tables and
     *                                   prefix each column.
     * @return \p810\MySQL\Query
     */
    public function select($columns = null): Query;

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
     * @param null|string $table The table that the query affects
     * @return \p810\MySQL\Query
     */
    public function update(?string $table = null): Query;

    /**
     * Returns an instance of \p810\MySQL\Query with a Delete builder
     * 
     * @param null|string $table The table that the query affects
     * @return \p810\MySQL\Query
     */
    public function delete(?string $table = null): Query;

    /**
     * Returns an instance of \p810\MySQL\Query with a Replace builder
     * 
     * @param array|null $columnsToValues A column => value array to pass to the builder
     * @return \p810\MySQL\Query
     */
    public function replace(?array $columnsToValues = null): Query;
}