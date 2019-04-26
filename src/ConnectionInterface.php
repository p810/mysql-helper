<?php

namespace p810\MySQL;

use PDO;

/**
 * Represents a connection to the database
 */
interface ConnectionInterface
{
    /**
     * Returns the connector that the class uses to communicate with the database
     * 
     * @return object
     */
    public function getConnector(): object;

    /**
     * Prepares the given query for execution by the database and returns a statement object
     * 
     * Returns null if the query could not be prepared
     * 
     * @return null|object
     */
    public function prepare(string $query): ?object;

    /**
     * Overrides the default \p810\MySQL\Processor that the class uses
     * 
     * @param \p810\MySQL\Processor $processor
     * @return void
     */
    public function setProcessor(Processor $processor): void;

    /**
     * Specifies a default query processor for all queries, or the given query type
     * 
     * @param callable $processor The callback to use when the query is executed
     * @param string   $command   An optional query type for which this processor should be used
     * @return void
     */
    public function setCommandHandler(callable $processor, string $command = '*'): void;

    /**
     * Returns a callback to process the result of a query
     * 
     * @return callable
     */
    public function getCommandHandler(string $command = '*'): callable;

    /**
     * Executes the given query and returns a statement object
     * 
     * Returns null if the query could not be prepared or executed
     * 
     * @param string $query
     * @param array  $input
     * @return null|object
     */
    public function query(string $query, array $input = []): ?object;

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