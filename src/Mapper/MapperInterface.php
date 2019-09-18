<?php

namespace p810\MySQL\Mapper;

use p810\MySQL\ConnectionInterface;

interface MapperInterface
{
    /**
     * Injects an instance of \p810\MySQL\ConnectionInterface into the mapper
     * 
     * @param \p810\MySQL\ConnectionInterface $connection The connection (or adapter)
     * @return void
     */
    function __construct(ConnectionInterface $connection);

    /**
     * Inserts a row with data from the given entity, and returns a boolean indicating success or failure
     * 
     * The user may manipulate the query by passing a callback, which should accept a `\p810\MySQL\Query` object as its
     * first argument and a `\p810\MySQL\Mapper\EntityInterface` as its second, and return the Query object to be
     * executed by the mapper
     * 
     * @param \p810\MySQL\Mapper\EntityInterface $entity The entity to transform
     * @return bool
     */
    public function create(EntityInterface $entity, ?callable $cb = null): bool;

    /**
     * Runs a select query and returns any results as entities
     * 
     * The user may manipulate the query by passing a callback, which should accept a `\p810\MySQL\Query` object as its
     * first argument, and return that object to be executed by the mapper
     * 
     * @param null|callable $cb An optional callback for manipulating the query
     * @return null|array
     */
    public function read(?callable $cb = null): ?array;

    /**
     * Runs an update query with data from the given entity, and criteria specified by manipulating the `Query` object
     * via a callback, which accepts the `Query` as its first argument and an `EntityInterface` as its second. This
     * callback should return the `Query` to be executed by the mapper.
     * 
     * Returns a boolean indicating whether the update was successful
     * 
     * @param \p810\MySQL\Mapper\EntityInterface $entity The entity whose row should be updated
     * @param null|callable $cb A callback used to manipulate the query; because the mapper
     *                          may specify criteria in its implementation, this is optional
     * @return bool
     */
    public function update(EntityInterface $entity, ?callable $cb = null): bool;

    /**
     * Runs a delete query using data from the given entity, and criteria specified by manipulating the `Query` object
     * via a callback, which accepts the `Query` as its first argument and an `EntityInterface` as its second. This
     * callback should return the `Query` to be executed by the mapper.
     * 
     * Returns a boolean indicating whether the deletion was successful
     * 
     * @param \p810\MySQL\Mapper\EntityInterface $entity The entity whose row should be deleted
     * @param null|callable $cb A callback used to manipulate the query; because the mapper
     *                          may specify criteria in its implementation, this is optional
     * @return bool
     */
    public function delete(EntityInterface $entity, ?callable $cb = null): bool;

    /**
     * Executes the given query with an optional array of input that MySQL should bind (for a prepared statement).
     * Returns either a boolean (false) or instance of `\PDOStatement`.
     * 
     * @param string $query The query to execute
     * @param array  $input An optional array of user input
     * @return null|object
     */
    public function query(string $query, array $input = []);
}
