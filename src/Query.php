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

    function __toString(): string {
        return $this->getQueryString();
    }

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
     * @return Row[]
     */
    public function execute(): array {
        if (! is_string($this->query)) {
            throw new Exception\QueryNotBuiltException;
        }

        return array_map(function ($row): Row {
            $row = new Row($row);
        }, static::$database->execute($this->query));
    }

    public static function select($columns = '*'): Builder {        
        $builder = new Select(new Query);

        $builder->setColumns($columns);
        
        return $builder;
    }
}