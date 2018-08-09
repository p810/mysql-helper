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
        return (! is_null(static::$database));
    }

    /**
     * @param array? $bindings Bindings for a prepared statement.
     * @return Row[]
     */
    public function execute(array $bindings = []): array {
        if (! is_string($this->query)) {
            throw new Exception\QueryNotBuiltException;
        }

        try {
            $statement = static::$database->prepare($this->query);
            
            $results = $statement->execute($bindings);
        } catch (\PDOException $e) {
            // do nothing -- we'll check for the return val of $statement
            // this is just to prevent a PDOException from stopping execution
        }

        if (! $statement || ! $results) {
            throw new Exception\QueryExecutionException;
        }

        return array_map(function ($row) {
            return new Row($row);
        }, $statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function select($columns = '*'): Builder {
        $builder = new Select(new Query);

        $builder->setColumns($columns);
        
        return $builder;
    }
}