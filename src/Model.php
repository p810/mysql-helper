<?php

namespace p810\MySQL;

use p810\MySQL\Builder\Select;

abstract class Model {
    /**
     * The table represented by this model.
     * @var string
     */
    protected $table;

    /**
     * An instance of PDO obtained from the
     * Connection instance.
     * @var \PDO
     */
    protected $database;

    function __construct(Connection $connection) {
        $this->database = $connection->getResource();

        if (! Query::isConnected()) {
            Query::setConnection($this->database);
        }
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