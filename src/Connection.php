<?php

namespace p810\MySQL;

use PDO;
use p810\MySQL\Processor\PdoProcessor;
use p810\MySQL\Processor\ProcessorInterface;

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
     * @var \p810\MySQL\Processor\ProcessorInterface
     */
    protected $processor;

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
        array $options = []
    ) {
        $arguments = [
            makePdoDsn($host, $database, $dsnParams),
            $user,
            $password
        ];

        if (! empty($options)) {
            $arguments[] = $options;
        }

        /** @psalm-suppress PossiblyInvalidArgument */
        $this->database = new PDO(...$arguments);

        if ($exceptions) {
            $this->shouldThrowExceptions();
        }

        $this->processor = new PdoProcessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnector(): object
    {
        return $this->database;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(string $query): ?object
    {
        $statement = $this->database->prepare($query);

        if (! $statement) {
            return null;
        }

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessor(): ProcessorInterface
    {
        return $this->processor;
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessor(ProcessorInterface $processor): void
    {
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $query, array $input = []): ?object
    {
        $statement = $this->prepare($query);

        if (! $statement) {
            return null;
        }

        $statement->execute($input);

        return $statement;
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
     * @param bool $throw Whether \PDO should throw exceptions on failure
     * @return self
     */
    public function shouldThrowExceptions(bool $throw = true): self
    {
        $errLevel = $throw ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT;

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
     * @return bool
     */
    public function transact(): bool
    {
        if (! $this->inTransaction()) {
            return $this->database->beginTransaction();
        }

        return true;
    }

    /**
     * An alias for \p810\MySQL\Connection::transact()
     * 
     * @throws \PDOException from \PDO::beginTransaction() if the attempt to start a transaction fails
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
        $query = new Query($this, new Builder\Select(), $this->processor);

        return $query->columns($columns ?? '*');
    }

    /**
     * @inheritdoc
     */
    public function insert(?array $columnsToValues = null): Query
    {
        $query = new Query($this, new Builder\Insert(), $this->processor);

        if ($columnsToValues) {
            $query->setColumnsAndValues($columnsToValues);
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function update(?string $table = null): Query
    {
        $query = new Query($this, new Builder\Update(), $this->processor);

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
        $query = new Query($this, new Builder\Delete(), $this->processor);

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
        $query = new Query($this, new Builder\Replace(), $this->processor);

        if ($columnsToValues) {
            $query->setColumnsAndValues($columnsToValues);
        }

        return $query;
    }
}
