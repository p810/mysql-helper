<?php

namespace p810\MySQL\Test;

use p810\MySQL\Query;
use p810\MySQL\Connection;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    use Credentials;
    use Mock\Connection;

    public function setUp()
    {
        $this->connection = $this->getMockConnection();
    }

    public function test_select_builder()
    {
        $query = $this->connection->select();

        $query->columns('*');
        $query->from('test_table');

        $this->assertEquals("SELECT * FROM test_table", $query->build());
    }
}