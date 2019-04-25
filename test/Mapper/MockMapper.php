<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\ConnectionInterface;
use p810\MySQL\Mapper\DefaultMapper;

class MockMapper extends DefaultMapper
{
    /**
     * {@inheritdoc}
     */
    public $table;

    /**
     * {@inheritdoc}
     */
    protected $entity = MockEntity::class;

    /**
     * {@inheritdoc}
     */
    public $key = 'test_id';

    /**
     * {@inheritdoc}
     */
    function __construct(ConnectionInterface $adapter)
    {
        $this->table = $_ENV['table'];

        parent::__construct($adapter);
    }
}
