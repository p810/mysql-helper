<?php

namespace p810\MySQL;

use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;

class Query {
    /**
     * The query string represented by this class.
     * @var string
     */
    protected $query;

    /**
     * The Builder object used to construct the query.
     * @var Builder\BuilderInterface
     */
    protected $builder;

    function __toString(): string {
        return $this->query;
    }

    protected function setBuilder(Builder $builder): self {
        $this->builder = $builder;

        return $this;
    }

    public function setQueryString(string $query): self {
        $this->query = $query;

        return $this;
    }

    /**
     * @todo: Populate this method with functionality to
     * tap into PDO and run the query.
     */
    public function execute() {}

    public function select($columns = '*'): Builder {
        $builder = new Select($this);

        $builder->setColumns($columns);

        $this->setBuilder($builder);
        
        return $builder;
    }
}