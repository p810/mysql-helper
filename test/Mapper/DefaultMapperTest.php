<?php

namespace p810\MySQL\Test\Mapper;

use PDO;
use PDOStatement;
use p810\MySQL\Query;
use p810\MySQL\Connection;
use PHPUnit\Framework\TestCase;
use p810\MySQL\Test\Credentials;
use p810\MySQL\ConnectionInterface;
use p810\MySQL\Mapper\DefaultAdapter;
use p810\MySQL\Mapper\EntityInterface;

use function sprintf;
use function microtime;

class DefaultMapperTest extends TestCase
{
    use Credentials;

    /**
     * @var \p810\MySQL\Mapper\MapperInterface
     */
    protected $mapper;

    public function setUp(): void
    {
        $this->mapper = new MockMapper($this->getConnection());
    }

    protected function getConnection(): ConnectionInterface
    {
        return new Connection($this->user, $this->password, $this->database, $this->host);
    }

    public function test_mapper_reads_data_without_criteria()
    {
        $entityList = $this->mapper->read();

        $this->assertIsArray($entityList);
        $this->assertContainsOnlyInstancesOf(MockEntity::class, $entityList);
    }

    public function test_mapper_reads_data_with_criteria()
    {
        $entityList = $this->mapper->read(function (Query $q) {
            return $q->limit(1);
        });

        $this->assertIsArray($entityList);
        $this->assertCount(1, $entityList);
    }

    public function test_mapper_finds_row_by_id(): EntityInterface
    {
        $entity = $this->mapper->findById(1);

        $this->assertInstanceOf(MockEntity::class, $entity);

        return $entity;
    }

    /**
     * @depends test_mapper_finds_row_by_id
     */
    public function test_mapper_updates_entity(EntityInterface $entity)
    {
        $entity->message = sprintf('Hello world! I am being updated at: %s', microtime(true));

        $result = $this->mapper->updateById(1, $entity);

        $this->assertTrue($result);
    }

    public function test_mapper_creates_row_from_entity()
    {
        $entity = new MockEntity('Hello everyone');

        $created = $this->mapper->create($entity);

        $this->assertTrue($created);
    }

    public function test_mapper_gets_last_inserted_id(): int
    {
        $id = $this->mapper->lastInsertId();

        $this->assertIsInt($id);

        return $id;
    }

    /**
     * @depends test_mapper_gets_last_inserted_id
     */
    public function test_mapper_deletes_by_id(int $id)
    {
        $deleted = $this->mapper->deleteById($id);

        $this->assertTrue($deleted);
    }
}