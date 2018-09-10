<?php

use p810\MySQL\Query;
use p810\MySQL\Builder\Delete;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class DeleteQueryBuilderTest extends TestCase {
    public function testQueryReturnsDeleteBuilder(): Delete {
        $query = Query::delete();

        $this->assertInstanceOf(Delete::class, $query);

        return $query;
    }

    /**
     * @depends testQueryReturnsDeleteBuilder
     */
    public function testTableIsSet(Delete $query): Delete {
        $query->from('test_table');

        $this->assertEquals('test_table', $query->getTable());

        return $query;
    }

    /**
     * @depends testTableIsSet
     */
    public function testClausesAreSet(Delete $query): Delete {
        $query->where('something_id', 1);

        $this->assertEquals('WHERE something_id = ?', $query->getWhere());

        return $query;
    }

    /**
     * @depends testClausesAreSet
     */
    public function testQueryString(Delete $query) {
        $this->assertEquals(
            'DELETE FROM test_table WHERE something_id = ?',
            $query->build()
        );
    }
}
