<?php

namespace p810\MySQL\Mapper;

use p810\MySQL\Query;

interface MapperInterface
{
    /**
     * Injects an instance of \p810\MySQL\Mapper\AdapterInterface for the mapper to source from
     * 
     * @param \p810\MySQL\Mapper\AdapterInterface $adapter
     */
    function __construct(AdapterInterface $adapter);

    /**
     * Runs a raw query and returns the result from PDO
     * 
     * @param string $query
     * @param array  $input
     * @return bool|\PDOStatement
     */
    public function query(string $query, array $input = []);

    /**
     * Inserts a new row with data from the given entity
     * 
     * @param \p810\MySQL\Mapper\EntityInterface $entity The entity from which data should be pulled
     * @return bool
     */
    public function create(EntityInterface $entity): bool;

    /**
     * Executes the given callback, providing it with a query builder object that should be modified
     * to get the desired data and returned
     * 
     * The returned query builder will be executed and, if successful, an array containing each row in
     * the result set as a \p810\MySQL\Mapper\EntityInterface object will be returned
     * 
     * The callback may be omitted to get the specified columns of all rows in the table
     * 
     * @param null|callable $cb      A callback used to modify the query
     * @param array|string  $columns An array or string specifying which columns to fetch
     * @return \p810\MySQL\Mapper\EntityInterface[]|null
     */
    public function get(?callable $cb = null, $columns = '*'): ?array;
}
