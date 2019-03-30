<?php

namespace p810\MySQL;

use PDO;
use PDOException;
use PDOStatement;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Update;
use p810\MySQL\Builder\Delete;
use p810\MySQL\Builder\Insert;
use p810\MySQL\Builder\Builder;
use p810\MySQL\Exception\TransactionCouldNotBeginException;

use function is_string;

class Query
{
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
     * An instance of \p810\MySQL\Connection.
     * @var \p810\MySQL\Connection
     */
    protected static $connection;

    /**
     * @throws \PDOException from PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if PDO::beginTransaction() returns false
     */
    public function transact(): self
    {
        if (! static::$database->inTransaction()) {
            static::$connection->beginTransaction();
        }
        return $this;
    }

    /**
     * @throws \PDOException from PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if PDO::beginTransaction() returns false
     */
    public function beginTransaction(): self
    {
        return $this->transact();
    }

    /**
     * @throws \PDOException if there isn't an active transaction
     */
    public function commit(): self
    {
        static::$connection->commit();
        
        return $this;
    }

    /**
     * @throws \PDOException if there isn't an active transaction
     */
    public function rollback(): self
    {
        static::$connection->rollback();

        return $this;
    }

    public function getQueryString(): ?string
    {
        return $this->query;
    }

    public function setQueryString(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getCursor(): PDO
    {
        return static::$database;
    }

    public static function setConnection(Connection $connection)
    {
        static::$database   = $connection->getResource();
        static::$connection = $connection;
    }

    public static function isConnected(): bool
    {
        return static::$database !== null;
    }

    public function execute(array $bindings = []): PDOStatement
    {
        if (! is_string($this->query)) {
            throw new Exception\QueryNotBuiltException;
        }

        try {
            $statement = static::$database->prepare($this->query);

            if ($statement instanceof PDOStatement) {
                $results = $statement->execute($bindings);
            }

            if (! $statement || ! $results) {
                throw new Exception\QueryExecutionException;
            }
        } catch (PDOException $e) {
            // do nothing -- we'll check for the return val of $statement
            // this is just to prevent a PDOException from stopping execution
        }

        return $statement;
    }

    public static function select($columns = '*'): Select
    {
        $builder = new Select(new Query);

        $builder->setColumns($columns);
        
        return $builder;
    }

    public static function delete(): Delete
    {
        $builder = new Delete(new Query);

        return $builder;
    }

    public static function update(string $table): Update
    {
        $builder = new Update(new Query);

        $builder->setTable($table);

        return $builder;
    }

    public static function insert(string $table): Insert
    {
        $builder = new Insert(new Query);

        $builder->setTable($table);

        return $builder;
    }

    private function __construct() {}
    private function __clone() {}
}