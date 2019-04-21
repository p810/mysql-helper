<?php

namespace p810\MySQL\Test\Mapper;

use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var \p810\MySQL\Test\Mapper\MockMapper
     */
    protected $mapper;

    public function setUp()
    {        
        $this->mapper = new MockMapper(new MockAdapter);
    }

    public function test_mapper_returns_entity_by_id()
    {
        $entity = $this->mapper->findById(1);

        $this->assertInstanceOf(MockEntity::class, $entity);
    }

    public function test_mapper_returns_entity_by_entity_value()
    {
        $entity = $this->mapper->findByMessage('I am the second row');

        $this->assertInstanceOf(MockEntity::class, $entity);
    }
}