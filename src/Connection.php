<?php

namespace p810\MySQL;

use PDO;
use PDOException;
use p810\MySQL\Exception\TransactionCouldNotBeginException;

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
     * @var callable[]
     */
    public $processors = [];

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

    /**
     * @inheritdoc
     */
    public function getPdo(): PDO
    {
        return $this->database;
    }

    /**
     * @throws \PDOException
     * @return false|\PDOStatement
     */
    public function prepare(string $query)
    {
        return $this->database->prepare($query);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProcessor(callable $processor, string $type = '*'): void
    {
        if ($type !== '*') {
            $type = strtolower($type);
        }

        $this->processors[$type] = $processor;
    }

    /**
     * @inheritdoc
     */
    public function raw(string $query, array $input = [])
    {
        $statement = $this->prepare($query);

        if ($statement) {
            $statement->execute($input);
        }

        return $statement;
    }

    /**
     * Returns a DSN to be passed into \PDO::__construct()
     * 
     * @param string $host      The hostname MySQL lives on
     * @param string $database  The database to use
     * @param array  $arguments An optional array of arguments
     * @return string
     */
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

    /**
     * Proxies a call to \PDO::setAttribute()
     * 
     * @param int   $attribute The attribute constant to set
     * @param mixed $value     The value to set the attribute to
     * @return self
     */
    public function setAttribute(int $attribute, $value): self
    {
        $this->database->setAttribute($attribute, $value);
        
        return $this;
    }

    /**
     * Tells \PDO to either throw exceptions or silently ignore warnings by setting \PDO::ATTR_ERRMODE
     * 
     * @param bool $shouldThrowExceptions Whether \PDO should throw exceptions on failure
     * @return self
     */
    public function shouldThrowExceptions(bool $shouldThrowExceptions = true): self
    {
        $errLevel = $shouldThrowExceptions ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT;

        $this->database->setAttribute(PDO::ATTR_ERRMODE, $errLevel);
        
        return $this;
    }

    /**
     * Tells \PDO whether the database should auto-commit results by setting \PDO::ATTR_AUTOCOMMIT
     * 
     * @param bool $shouldAutoCommit Whether the database should auto-commit results
     * @return self
     */
    public function shouldAutoCommit(bool $shouldAutoCommit = true): self
    {
        $this->autocommit = $shouldAutoCommit;
        
        $this->database->setAttribute(PDO::ATTR_AUTOCOMMIT, $shouldAutoCommit);

        return $this;
    }

    /**
     * Begins a transaction with the database 
     * 
     * @throws \PDOException from \PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if \PDO::beginTransaction() returns false
     * @return bool
     */
    public function transact(): bool
    {
        if (! $this->inTransaction() && ! $this->database->beginTransaction()) {
            throw new TransactionCouldNotBeginException;
        }

        return true;
    }

    /**
     * An alias for \p810\MySQL\Connection::transact()
     * 
     * @throws \PDOException from \PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if \PDO::beginTransaction() returns false
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->transact();
    }

    /**
     * Returns a boolean indicating whether MySQL is currently in a transaction
     * 
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->database->inTransaction();
    }

    /**
     * Commits the results of the queries executed in the current transaction
     * 
     * @throws \PDOException if there isn't an active transaction
     * @return bool
     */
    public function commit(): bool
    {
        return $this->database->commit();
    }

    /**
     * Reverts the results of the queries executed in the current transaction
     * 
     * @throws \PDOException if there isn't an active transaction
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->database->rollBack();
    }

    /**
     * @inheritdoc
     */
    public function select($columns = null): Query
    {
        $query = new Query($this, new Builder\Select);

        return $query->columns($columns ?? '*');
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function update(?string $table = null): Query
    {
        $query = new Query($this, new Builder\Update);

        if ($table) {
            $query->table($table);
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function delete(?string $table = null): Query
    {
        $query = new Query($this, new Builder\Delete);

        if ($table) {
            $query->from($table);
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function replace(?array $columnsToValues = null): Query
    {
        $query = new Query($this, new Builder\Replace);

        if ($columnsToValues) {
            $columns = array_keys($columnsToValues);
            $values  = array_values($columnsToValues);

            $query->columns($columns);
            $query->values($values);
        }

        return $query;
    }
}