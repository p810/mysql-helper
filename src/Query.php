<?php

namespace p810\MySQL;

use \PDO;
use \PDOStatement;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Update;
use p810\MySQL\Builder\Delete;
use p810\MySQL\Builder\Insert;
use p810\MySQL\Builder\Builder;

class Query {
    /**
     * The query string represented by this class.
     * @var string
     */
    protected $query;

    /**
     * A PDO resource from the Connection object.
     * @var \PDO
     */
    protected static $database;

    /**
     * Disallow direct instantiation. Requires the use of
     * one of the static command methods.
     */
    private function __construct() {}

    public function getQueryString(): ?string {
        return $this->query;
    }

    public function setQueryString(string $query): self {
        $this->query = $query;

        return $this;
    }

    public static function setConnection(Connection $connection) {
        static::$database = $connection->getResource();
    }

    /**
     * Used primarily in Model classes, to tell whether the database/PDO
     * link needs to be updated - but could be used anywhere that this
     * check is useful.
     */
    public static function isConnected(): bool {
        return static::$database !== null;
    }

    /**
     * @param array? $bindings Bindings for a prepared statement.
     * @return Row[]
     */
    public function execute(array $bindings = []): PDOStatement {
        if (! is_string($this->query)) {
            throw new Exception\QueryNotBuiltException;
        }

        try {
            $statement = static::$database->prepare($this->query);
            
            $results = $statement->execute($bindings);

            if (! $statement || ! $results) {
                throw new Exception\QueryExecutionException;
            }
        } catch (\PDOException $e) {
            // do nothing -- we'll check for the return val of $statement
            // this is just to prevent a PDOException from stopping execution
        }

        return $statement;
    }

    public static function select($columns = '*'): Select {
        $builder = new Select(new Query);

        $builder->setColumns($columns);
        
        return $builder;
    }

    public static function delete(): Delete {
        $builder = new Delete(new Query);

        return $builder;
    }

    public static function update(string $table): Update {
        $builder = new Update(new Query);

        $builder->setTable($table);

        return $builder;
    }

    public static function insert(string $table): Insert {
        $builder = new Insert(new Query);

        $builder->setTable($table);

        return $builder;
    }
}