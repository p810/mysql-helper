<?php

use p810\MySQL\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase {
    /**
     * @expectedException p810\MySQL\Exception\ConnectionException
     */
    public function testConnectionExceptionIsRaised() {
        new Connection('root', '', 'database', 'invalidhost');
    }

    public function testConnectionReturnsPdoInstance(): Connection {
        $connection = new Connection('root', 'root', 'test', 'database');

        $this->assertInstanceOf(PDO::class, $connection->getResource());

        return $connection;
    }
}