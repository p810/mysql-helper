<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Mapper\RowMapper;
use p810\MySQL\ConnectionInterface;

class MockRowMapper extends RowMapper
{
    /**
     * {@inheritdoc}
     */
    public $key = 'test_id';

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
    function __construct(ConnectionInterface $adapter)
    {
        $this->table = $_ENV['table'];

        parent::__construct($adapter);
    }
}
