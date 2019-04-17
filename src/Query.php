<?php

namespace p810\MySQL;

use PDOStatement;
use BadMethodCallException;
use p810\MySQL\Builder\Builder;

use function method_exists;

class Query
{
    /**
     * @var false|\PDOStatement
     */
    public $statement;

    /**
     * @var \p810\MySQL\Builder\Builder
     */
    protected $builder;

    /**
     * @var \p810\MySQL\ConnectionInterface
     */
    protected $database;

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
     * Sets and executes a prepared query
     * 
     * @return bool
     */
    public function execute(): bool
    {
        $this->statement = $this->database->prepare( $this->builder->build() );

        if (! $this->statement instanceof PDOStatement) {
            return false;
        }

        return $this->statement->execute($this->builder->input);
    }
}