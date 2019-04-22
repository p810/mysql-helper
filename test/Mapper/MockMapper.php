<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Mapper\DefaultMapper;
use p810\MySQL\Mapper\MapperInterface;
use p810\MySQL\Mapper\EntityInterface;

class MockMapper extends DefaultMapper
{
    /**
     * {@inheritdoc}
     */
    public $table = 'test_table';

    /**
     * {@inheritdoc}
     */
    protected $entity = MockEntity::class;

    /**
     * {@inheritdoc}
     */
    public $key = 'test_id';
}
