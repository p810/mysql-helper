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

    final public function execute(): ?ResultSet {
        if (! is_string($this->query->getQueryString())) {
            $this->query->setQueryString( $this->build() );
        }
        
        $rows = $this->query->execute($this->bindings);

        if (! empty($rows)) {
            $set = new ResultSet;

            foreach ($rows as $row) {
                $set->attach($row);
            }
        }

        return empty($rows) ? null : $set;
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
}