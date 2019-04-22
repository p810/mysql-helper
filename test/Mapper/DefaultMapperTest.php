<?php

namespace p810\MySQL\Test\Mapper;

use PDO;
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

    /**
     * @var \p810\MySQL\Mapper\AdapterInterface
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new DefaultAdapter($this->getConnection());

        $this->mapper = new MockMapper($this->adapter);
    }

    protected function getConnection(): ConnectionInterface
    {
        return new Connection($this->user, $this->password, $this->database, $this->host);
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

    public function test_mapper_creates_row_from_entity(): EntityInterface
    {
        $entity = new MockEntity('Hello everyone');

        $created = $this->mapper->create($entity);

        $this->assertTrue($created);

        return $entity;
    }

    /**
     * @todo I feel like there should be a cleaner solution to this?
     */
    public function test_mapper_deletes_by_id()
    {
        $query = $this->adapter->query("select test_id from test_table order by test_id desc limit 1");
        $result = $query->fetch(PDO::FETCH_ASSOC);

        $deleted = $this->mapper->deleteById($result['test_id']);

        $this->assertTrue($deleted);
    }
}