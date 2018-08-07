<?php

namespace p810\MySQL;

use \PDO;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;

class Query {
    /**
     * The query string represented by this class.
     * @var string
     */
    protected $query;

    /**
     * Disallow direct instantiation. Requires the use of
     * one of the static command methods.
     */
    private function __construct() {}

    function __toString(): string {
        return $this->getQueryString();
    }

    public function getQueryString(): string {
        if ($query == null) {
            throw new Exception\UnexecutedQueryException;
        }

        return $this->query;
    }

    public function setQueryString(string $query): self {
        $this->query = $query;

        return $this;
    }

    public static function select($columns = '*'): Builder {        
        $builder = new Select(new Query);

        $builder->setColumns($columns);
        
        return $builder;
    }
}