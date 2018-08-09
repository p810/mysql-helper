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

    function __construct(Query $query) {
        $this->query = $query;
    }

    final public function execute(): ResultSet {
        if (! is_string($this->query->getQueryString())) {
            $this->query->setQueryString( $this->build() );
        }
        
        $rows = $this->query->execute();

        return new ResultSet($rows);
    }

    /**
     * Using the Query object passed into BuilderInterface::setQuery()
     * the class that implements this interface should be able to construct
     * a query string via this method.
     */
    abstract public function build(): string;
}