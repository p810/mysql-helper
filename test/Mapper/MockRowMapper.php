<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Test\Credentials;
use p810\MySQL\Mapper\RowMapper;
use p810\MySQL\ConnectionInterface;

class MockRowMapper extends RowMapper
{
    use Credentials;

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
        $this->loadDatabaseCredentials();
        parent::__construct($adapter);
    }
}
