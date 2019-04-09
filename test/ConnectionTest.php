<?php

namespace p810\MySQL\Test;

use PDO;
use PDOException;
use p810\MySQL\Connection;
use PHPUnit\Framework\TestCase;
use p810\MySQL\Exception\ConnectionException;
use p810\MySQL\Exception\TransactionCouldNotBeginException;

class ConnectionTest extends TestCase
{
    use Credentials;

    /**
     * @var \p810\MySQL\Connection
     */
    protected $connection;

    public function setUp()
    {
        $this->connection = new Connection($this->user, $this->password, $this->database, $this->host);
    }

    public function test_connection_returns_pdo() {
        $this->assertInstanceOf(PDO::class, $this->connection->getPdo());
    }

    public function test_connection_begins_transaction() {
        $this->assertTrue($this->connection->transact());
        $this->assertTrue($this->connection->inTransaction());
        $this->assertFalse($this->connection->beginTransaction());
    }
}