<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Test\Credentials;
use p810\MySQL\ConnectionInterface;
use p810\MySQL\Mapper\DefaultMapper;

class MockMapper extends DefaultMapper
{
    use Credentials;

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
        $this->loadDatabaseCredentials();
        parent::__construct($adapter);
    }
}
