<?php

namespace p810\MySQL;

use BadMethodCallException;
use p810\MySQL\Builder\Builder;
use p810\MySQL\Processor\ProcessorInterface;

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
     * @var \p810\MySQL\Processor\ProcessorInterface
     */
    protected $processor;

    /**
     * Specifies the Connection and Builder that the query should use
     * 
     * @param \p810\MySQL\ConnectionInterface $database 
     * @param \p810\MySQL\Builder\Builder $builder
     * @param \p810\MySQL\Processor\ProcessorInterface $processor
     * @return void
     */
    function __construct(
        ConnectionInterface $database,
        Builder $builder,
        ProcessorInterface $processor
    ) {
        $this->database = $database;
        $this->builder = $builder;
        $this->processor = $processor;
    }

    /**
     * Proxies calls on this object to the injected Builder if the method doesn't exist
     * 
     * @param string $method The method being called
     * @param array $arguments An optional, variadic list of arguments
     * @return mixed
     * @throws \BadMethodCallException if the method is not defined in Query or the injected builder object
     */
    function __call(string $method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$arguments);
        }

        if (method_exists($this->builder, $method)) {
            $this->builder->$method(...$arguments);

            return $this;
        }

        throw new BadMethodCallException();
    }

    /**
     * Executes a prepared query and returns the result
     * 
     * @param null|callable $handler An optional callback used to process the result of the query
     * @param bool $callbackOnBool Whether to call the user-supplied $processor when \PDOStatement::execute() returns
     *                             false
     * @return mixed
     */
    public function execute(?callable $handler = null, bool $callbackOnBool = false)
    {
        $statement = $this->database->query(
            $this->builder->build(),
            $this->builder->input
        );

        if ($statement || $callbackOnBool) {
            $statement = $statement ?: null;

            $callback = $handler ?? $this->processor->getHandler($this->builder::COMMAND);

            return $callback($statement);
        }

        return null;
    }
}
