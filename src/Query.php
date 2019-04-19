<?php

namespace p810\MySQL;

use PDOStatement;
use BadMethodCallException;
use p810\MySQL\Builder\Builder;

use function strtolower;
use function method_exists;

class Query
{
    /**
     * @var \p810\MySQL\Builder\Builder
     */
    protected $builder;

    /**
     * @var \p810\MySQL\ConnectionInterface
     */
    protected $database;

    /**
     * @var array<string,callable>
     */
    protected $processor = [];

    /**
     * Specifies the Connection and Builder that the query should use
     * 
     * @param \p810\MySQL\ConnectionInterface $database The Connection to use
     * @param \p810\MySQL\Builder\Builder     $builder  The Builder instance to use
     * @return void
     */
    function __construct(ConnectionInterface $database, Builder $builder)
    {
        $this->database = $database;
        $this->builder = $builder;
    }

    /**
     * Proxies calls on this object to the injected Builder if the method
     * doesn't exist
     * 
     * @param string $method    The method being called
     * @param array  $arguments An optional, variadic list of arguments
     * @return mixed
     * @throws \BadMethodCallException if the method is not defined in Query or the injected Builder object
     */
    function __call(string $method, array $arguments)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$arguments);
        }

        if (method_exists($this->builder, $method)) {
            $this->builder->$method(...$arguments);

            return $this;
        }

        throw new BadMethodCallException;
    }

    /**
     * Executes a prepared query and returns the result
     * 
     * @param null|callable $processor      An optional callback used to process the result of the query
     * @param bool          $callbackOnBool Whether to call the user-supplied $processor when \PDOStatement::execute() returns false
     * @return mixed
     */
    public function execute(?callable $processor = null, bool $callbackOnBool = false)
    {
        $statement = $this->database->prepare($this->builder->build());

        if (! $statement instanceof PDOStatement) {
            return false;
        }

        $result = $statement->execute($this->builder->input);
        $callback = $processor ?? $this->getDefaultProcessor();

        if ($result || ($callbackOnBool && $processor)) {
            $result = $callback($statement);
        }

        return $result;
    }

    /**
     * Returns the proper callback to handle a \PDOStatement
     * 
     * The order of precedence in which a processor is chosen is:
     *  1. A callback registered explicitly for the type of query
     *  2. A callback registered to handle every type of query
     *  3. The Builder object's `process()` method
     * 
     * @return callable
     */
    protected function getDefaultProcessor(): callable
    {
        return $this->processor[$this->builder->type] ?? $this->processor['*'] ?? [$this->builder, 'process'];
    }

    /**
     * Overrides the default processor for the given query type
     * If no type is provided it will be registered to handle each type
     * 
     * @param callable $processor A callback used to process the result of a \PDOStatement
     * @param string   $type      The type of query that this callback should handle
     * @return self
     */
    public function setDefaultProcessor(callable $processor, string $type = '*'): self
    {
        if ($type !== '*') {
            $type = strtolower($type);
        }

        $this->processor[$type] = $callback;

        return $this;
    }
}