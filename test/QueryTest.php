<?php

namespace p810\MySQL\Test;

use PDO;
use Closure;
use stdClass;
use PDOStatement;
use p810\MySQL\Query;
use p810\MySQL\Connection;
use p810\MySQL\Builder\Insert;
use p810\MySQL\Builder\Select;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    use Credentials;

    /**
     * @var \p810\MySQL\Connection
     */
    protected $connection;

    public function setUp(): void
    {
        $this->connection = new Connection($this->user, $this->password, $this->database, $this->host);
    }

    public function test_default_processor_returns_array()
    {
        $query = $this->connection->select()->from($this->table)->limit(1);

        $this->assertIsArray($query->execute());
    }

    public function test_default_processor_returns_row_count()
    {
        $query = $this->connection->insert(['message' => 'Hey there!'])->into($this->table);

        $this->assertEquals(1, $query->execute());
    }

    public function test_default_processor_overridden_by_wildcard()
    {
        $this->connection->setDefaultProcessor(function (PDOStatement $statement) {
            return $statement->fetch(PDO::FETCH_OBJ);
        });

        $query = $this->connection->select()->from($this->table)->limit(1);

        $this->assertInstanceOf(stdClass::class, $query->execute());
    }

    public function test_wildcard_processor_overridden_by_specific_processor()
    {
        $this->connection->setDefaultProcessor(function (PDOStatement $statement) {
            return 'Hello world!';
        }, 'select');

        $query = $this->connection->select()->from($this->table);

        $this->assertEquals('Hello world!', $query->execute());
    }

    public function test_user_supplied_processor_overrides_all()
    {
        $query = $this->connection->select()->from($this->table);

        $result = $query->execute(function (PDOStatement $statement) {
            return 'Hello universe!';
        });

        $this->assertEquals('Hello universe!', $result);
    }
}