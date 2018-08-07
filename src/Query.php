<?php

namespace p810\MySQL;

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

    public function get(): self {
        if (! ($this->builder instanceof Builder)) {
            /** @todo: Need a better name for this exception */
            throw new \Exception;
        }

        $this->query = $this->builder->build();

        return $this;
    }

    public function select($columns = '*'): Builder {
        $builder = new Builder\Select($this);

        $builder->setColumns($columns);

        $this->setBuilder($builder);
        
        return $builder;
    }
}