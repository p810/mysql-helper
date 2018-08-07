<?php

namespace p810\MySQL\Builder;

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

    function __construct(\p810\MySQL\Query $query) {
        $this->query = $query;
    }

    /**
     * Using the Query object passed into BuilderInterface::setQuery()
     * the class that implements this interface should be able to construct
     * a query string via this method.
     */
    abstract public function build(): string;
}