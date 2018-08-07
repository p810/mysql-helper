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

    public function execute(Query $query) {
        return $this->database->execute( $query->build() );
    }

    public function where(...$clauses): Select {
        $query = Query::select();

        $query->from( $this->getTable() );

        if (count($clauses) === 1) {
            $clauses = array_shift($clauses);
        }
        
        $query->where($clauses);

        return $query;
    }
}