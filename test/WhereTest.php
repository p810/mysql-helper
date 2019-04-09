<?php

use p810\MySQL\Query\Where;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for queries that can contain WHERE clauses.
 */
class WhereTest extends TestCase
{
    public function getMockQuery()
    {
        return new class {
            use \p810\MySQL\Query\Where;

            public function build(): string
            {
                return $this->compileWhere();
            }
        };
    }

    public function test_where_string_builds()
    {
        $query = $this->getMockQuery();
        
        $query->where('a', 'b')
          ->or()
          ->whereNotEquals('c', 'd')
          ->whereGreaterOrEqual('e', '1', 'OR')
          ->whereIn('f', ['g', 'h', 'i'])
          ->and() // this is redundant, but just to make sure it works
          ->whereLike('j', '%k%');
        
        $this->assertEquals('WHERE a = ? OR c != ? OR e >= ? AND f IN (?, ?, ?) AND j LIKE ?', $query->build());
    }
}