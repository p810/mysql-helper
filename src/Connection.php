<?php

namespace p810\MySQL;

use PDO;
use PDOException;

class Connection implements ConnectionInterface
{
    /**
     * @var \PDO
     */
    protected $database;

    /**
     * @var bool
     */
    protected $autocommit;

    /**
     * @param string $user       The user to connect to MySQL with
     * @param string $password   The password of the MySQL user
     * @param string $database   The database to connect to
     * @param string $host       The hostname of the MySQL server
     * @param bool   $exceptions Specifies whether \PDO should raise an exception when it encounters an error
     * @param array  $dsnParams  Optional parameters to pass to the DSN used when instantiating \PDO
     * @param array  $options    Optional arguments to include in the construction of the \PDO instance
     * @throws \PDOException if the database connection failed
     */
    function __construct(
        string $user,
        string $password,
        string $database,
        string $host = '127.0.0.1',
        bool $exceptions = true,
        array $dsnParams = [],
        array $options = [])
    {
        $arguments = [$this->getDsn($host, $database, $dsnParams), $user, $password];

        if (! empty($options)) {
            $arguments[] = $options;
        }

        /** @psalm-suppress PossiblyInvalidArgument */
        $this->database = new PDO(...$arguments);

        if ($exceptions) {
            $this->shouldThrowExceptions();
        }
    }

    public function getPdo(): PDO
    {
        return $this->database;
    }

    private function getDsn(string $host, string $database, array $arguments = []): string
    {
        $dsn = "mysql:host=$host;dbname=$database";

        if ($arguments) {
            foreach ($arguments as $argument => $value) {
                $dsn .= ";$argument=$value";
            }
        }

        return $dsn;
    }

    public function setAttribute(int $attribute, $value): self
    {
        $this->database->setAttribute($attribute, $value);
        
        return $this;
    }

    public function shouldThrowExceptions(bool $shouldThrowExceptions = true): self
    {
        $errLevel = $shouldThrowExceptions ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT;

        $this->database->setAttribute(PDO::ATTR_ERRMODE, $errLevel);
        
        return $this;
    }

    public function shouldAutoCommit(bool $shouldAutoCommit = true): self
    {
        $this->autocommit = $shouldAutoCommit;
        
        $this->database->setAttribute(PDO::ATTR_AUTOCOMMIT, $shouldAutoCommit);

        return $this;
    }

    /**
     * @throws \PDOException from \PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if \PDO::beginTransaction() returns false
     */
    public function transact(): bool
    {
        if (! $this->database->inTransaction()) {
            if (! $this->database->beginTransaction()) {
                throw new Exception\TransactionCouldNotBeginException();
            }
            return true;
        }
        return false;
    }

    /**
     * @throws \PDOException from \PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if \PDO::beginTransaction() returns false
     */
    public function beginTransaction(): bool
    {
        return $this->transact();
    }

    public function inTransaction(): bool
    {
        return $this->database->inTransaction();
    }

    /**
     * @throws \PDOException if there isn't an active transaction
     */
    public function commit(): bool
    {
        return $this->database->commit();
    }

    /**
     * @throws \PDOException if there isn't an active transaction
     */
    public function rollback(): bool
    {
        return $this->database->rollBack();
    }

    public function select(): Query
    {
        return new Query($this, new Builder\Select);
    }

    public function insert(?array $columnsToValues = null): Query
    {
        $query = new Query($this, new Builder\Insert);

        if ($columnsToValues) {
            $columns = array_keys($columnsToValues);
            $values  = array_values($columnsToValues);

            $query->columns($columns);
            $query->values($values);
        }

        return $query;
    }

    public function update(): Query
    {
        return new Query($this, new Builder\Update);
    }

    public function delete(): Query
    {
        return new Query($this, new Builder\Delete);
    }
}