<?php

namespace p810\MySQL\Test;

use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    use Credentials;
    use Mock\Connection;

    public function setUp()
    {
        $this->connection = $this->getMockConnection();
    }

    public function test_select_builder_simple()
    {
        $query = $this->connection->select();

        $query->columns('*');
        $query->from('test_table');

        $this->assertEquals("SELECT * FROM test_table", $query->build());
    }

    public function test_select_builder_with_where_clause()
    {
        $query = $this->connection->select();

        $query->columns('*')
              ->from('test_table')
              ->where('a', 'b')
              ->or()
              ->whereNotEquals('c', 'd')
              ->whereGreaterOrEqual('e', '1', 'OR')
              ->whereIn('f', ['g', 'h', 'i'])
              ->and()
              ->whereLike('j', '%k%');

        $this->assertEquals('SELECT * FROM test_table WHERE a = ? OR c != ? OR e >= ? AND f IN (?, ?, ?) AND j LIKE ?', $query->build());
    }
}