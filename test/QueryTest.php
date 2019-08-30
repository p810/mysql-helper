<?php

namespace p810\MySQL\Test;

use PDO;
use Closure;
use stdClass;
use PDOStatement;
use p810\MySQL\Query;
use p810\MySQL\Connection;
use p810\MySQL\PdoProcessor;
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

    public function test_select_processor_returns_array()
    {
        $query = $this->connection->select()->from($this->table)->limit(1);

        $this->assertIsArray($query->execute());
    }

    public function test_wildcard_processor_returns_row_count()
    {
        $query = $this->connection->insert(['message' => 'Hey there!'])->into($this->table);

        $this->assertEquals(1, $query->execute());
    }

    public function test_select_processor_is_overridden()
    {   
        $this->connection->getProcessor()->setHandler(function (PDOStatement $statement) {
            return $statement->fetch(PDO::FETCH_OBJ);
        }, 'select');

        $query = $this->connection->select()->from($this->table)->limit(1);

        $this->assertInstanceOf(stdClass::class, $query->execute());
    }

    public function test_user_supplied_processor_overrides_all()
    {
        $query = $this->connection->select()->from($this->table);

        $result = $query->execute(function (PDOStatement $statement) {
            return 'Hello world!';
        });

        $this->assertEquals('Hello world!', $result);
    }
}
