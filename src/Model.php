<?php

namespace p810\MySQL;

use p810\MySQL\Builder\Select;

abstract class Model {
    /**
     * The table represented by this model.
     * @var string
     */
    protected $table;

    function __construct(Connection $connection) {
        $this->database = $connection->getResource();
    }

    final public function getTable(): string {
        return $this->table;
    }

    public function where(...$clauses): Select {
        $query = Query::select();

        $query->from( $this->getTable() );
        
        $query->where(...$clauses);

        return $query;
    }
}