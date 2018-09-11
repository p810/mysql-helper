<?php

namespace p810\MySQL\Builder;

use \p810\MySQL\Query;
use \p810\MySQL\ResultSet;

abstract class Builder {
    /**
     * An associative array mapping parts of a
     * query string to their values.
     * @var string[]
     */
    protected $fragments;

    /**
     * The Query class used to instantiate the Builder.
     * @var p810\MySQL\Query
     */
    protected $query;

    /**
     * Bindings for prepared statements.
     * @var mixed[]
     */
    protected $bindings;

    function __construct(Query $query) {
        $this->query = $query;
    }

    final public function execute() {
        if (! is_string($this->query->getQueryString())) {
            $this->query->setQueryString( $this->build() );
        }

        $statement = $this->query->execute($this->bindings);

        if (! $statement) {
            return null;
        }

        return $this->handleResults($statement);
    }

    protected function bind($value): self {
        $this->bindings[] = $value;

        return $this;
    }

    public function getBindings(): array {
        return $this->bindings;
    }

    /**
     * Using the Query object passed into BuilderInterface::setQuery()
     * the class that implements this interface should be able to construct
     * a query string via this method.
     */
    abstract public function build(): string;

    /**
     * After a query is successfully executed, this method will be called.
     * 
     * @todo Come up with a way to invoke additional callbacks
     * @return mixed
     */
    abstract public function handleResults(\PDOStatement $statement);
}