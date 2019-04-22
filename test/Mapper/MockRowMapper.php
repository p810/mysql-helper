<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Mapper\RowMapper;

class MockRowMapper extends RowMapper
{
    /**
     * {@inheritdoc}
     */
    public $key = 'test_id';

    /**
     * {@inheritdoc}
     */
    public $table = 'test_table';

    /**
     * {@inheritdoc}
     */
    protected $entity = MockEntity::class;
}
