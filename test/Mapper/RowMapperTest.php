<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Connection;
use p810\MySQL\Mapper\Row;
use PHPUnit\Framework\TestCase;
use p810\MySQL\Test\Credentials;
use p810\MySQL\ConnectionInterface;
use p810\MySQL\Mapper\DefaultAdapter;

use function sprintf;
use function microtime;

class RowMapperTest extends TestCase
{
    use Credentials;

    /**
     * @var \p810\MySQL\Mapper\MapperInterface
     */
    protected $mapper;

    protected function getConnection(): ConnectionInterface
    {
        return new Connection($this->user, $this->password, $this->database, $this->host);
    }

    public function setUp(): void
    {
        $this->mapper = new MockRowMapper($this->getConnection());
    }

    public function test_mapper_returns_row()
    {
        $row = $this->mapper->findById(1);

        $this->assertInstanceOf(Row::class, $row);
        $this->assertEquals(1, $row->test_id);

        return $row;
    }

    /**
     * @depends test_mapper_returns_row
     */
    public function test_row_is_saved_after_update(Row $row): string
    {
        $message = $row->message = sprintf('Hello world! I am being updated at: %s', microtime(true));

        $saved = $row->save();

        $this->assertTrue($saved);

        return $message;
    }

    /**
     * @depends test_row_is_saved_after_update
     */
    public function test_update_persisted(string $message)
    {
        $row = $this->mapper->findById(1);

        $this->assertEquals($message, $row->message);
    }

    public function test_mapper_creates_row()
    {
        $entity = new MockEntity('Hello world! I am being created during a test case');
        
        $created = $this->mapper->create($entity);

        $this->assertTrue($created);

        return $this->mapper->lastInsertId();
    }

    /**
     * @depends test_mapper_creates_row
     */
    public function test_mapper_deletes_row($id)
    {
        $row = $this->mapper->findById($id);
        
        $deleted = $row->delete();

        $this->assertTrue($deleted);
    }
}