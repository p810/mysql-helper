<?php

namespace p810\MySQL\Test;

use PDO;
use PDOException;
use p810\MySQL\Connection;
use PHPUnit\Framework\TestCase;
use p810\MySQL\Exception\ConnectionException;
use p810\MySQL\Exception\TransactionCouldNotBeginException;

class ConnectionTest extends TestCase {
    use Credentials;

    /**
     * @expectedException p810\MySQL\Exception\ConnectionException
     */
    public function testConnectionExceptionIsRaised() {
        $this->expectException(ConnectionException::class);
        
        new Connection('root', '', 'database', 'invalidhost');
    }

    public function testConnectionReturnsPdoInstance() {
        $connection = new Connection($this->user, $this->password, $this->database, $this->host);

        $this->assertInstanceOf(PDO::class, $connection->getResource());
    }

    public function testConnectionBeginsTransaction() {
        $connection = new Connection($this->user, $this->password, $this->database, $this->host);

        $this->assertTrue($connection->transact());
        $this->assertTrue($connection->inTransaction());
        $this->assertFalse($connection->beginTransaction());
    }
}