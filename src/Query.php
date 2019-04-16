<?php

namespace p810\MySQL;

use PDOStatement;
use BadMethodCallException;
use p810\MySQL\Builder\Builder;
use p810\MySQL\Exception\QueryExecutionException;

use function method_exists;

class Query
{
    /**
     * @var \PDOStatement|null
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

    function __construct(ConnectionInterface $database, Builder $builder)
    {
        $this->database = $database;
        $this->builder = $builder;
    }

    /**
     * @throws \BadMethodCallException if the method is not defined in Query or the injected Builder object
     */
    function __call(string $method, array $arguments)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$arguments);
        }

        if (method_exists($this->builder, $method)) {
            return $this->builder->$method(...$arguments);
        }

        throw new BadMethodCallException;
    }

    public function execute(): bool
    {
        $this->statement = $this->database->prepare( $this->builder->build() );

        // in case the user has turned off ERRMODE_EXCEPTION, we don't want to
        // try to call execute() on a boolean, and the user should know that
        // their query failed
        if (! $this->statement instanceof PDOStatement) {
            throw new QueryExecutionException;
        }

        return $this->statement->execute($this->builder->input);
    }
}