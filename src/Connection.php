<?php

namespace p810\MySQL;

use PDO;
use PDOException;
use p810\MySQL\Exception\ConnectionException;
use p810\MySQL\Exception\TransactionCouldNotBeginException;

class Connection {
    /**
     * Database object returned by PDO.
     * @var \PDO
     */
    protected $database;

    /**
     * Whether to autocommit queries.
     * @var bool
     */
    public $autocommit;

    function __construct(
        string $username, string $password, string $database,
        string $host = '127.0.0.1', ?array $options = null
    ) {
        $arguments = [sprintf('mysql:host=%s;dbname=%s', $host, $database), $username, $password];

        if ($options) {
            $arguments[] = $options;
        }
        
        try {
            $this->database = new PDO(...$arguments);
        } catch (PDOException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getResource(): PDO {
        return $this->database;
    }

    public function autocommit(bool $shouldAutoCommit = true): self {
        $this->autocommit = $shouldAutoCommit;
        $this->database->setAttribute(PDO::ATTR_AUTOCOMMIT, $shouldAutoCommit);
        
        return $this;
    }

    /**
     * @throws \PDOException from PDO::beginTransaction() if the attempt to start a transaction fails
     * @throws \p810\MySQL\Exception\TransactionCouldNotBeginException if PDO::beginTransaction() returns false
     */
    public function transact(): bool {
        if (! $this->database->inTransaction()) {
            if (! $this->database->beginTransaction()) {
                throw new TransactionCouldNotBeginException();
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction(): bool {
        return $this->transact();
    }

    public function inTransaction(): bool {
        return $this->database->inTransaction();
    }

    /**
     * @throws \PDOException if there isn't an active transaction
     */
    public function commit(): bool {
        return $this->database->commit();
    }

    /**
     * @throws \PDOException if there isn't an active transaction
     */
    public function rollback(): bool {
        return $this->database->rollBack();
    }
}