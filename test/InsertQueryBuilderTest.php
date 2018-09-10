<?php

use p810\MySQL\Query;
use p810\MySQL\Builder\Insert;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class InsertQueryBuilderTest extends TestCase {
    public function testQueryReturnsInsertBuilder(): Insert {
        $query = Query::insert('test_table');

        $this->assertInstanceOf(Insert::class, $query);
        $this->assertEquals('test_table', $query->getTable());

        return $query;
    }

    /**
     * @depends testQueryReturnsInsertBuilder
     */
    public function testColumnsAreSet(Insert $query): Insert {
        $query->columns(['x', 'y']);

        $this->assertEquals('x, y', $query->getColumns());

        return $query;
    }

    /**
     * @depends testColumnsAreSet
     */
    public function testValuesAreSet(Insert $query): Insert {
        $query->values([
            'foo' => 'hello',
            'bar' => 'world'
        ]);

        $this->assertEquals('?, ?', $query->getValues());

        return $query;
    }

    /**
     * @depends testValuesAreSet
     */
    public function testQueryString(Insert $query) {
        $this->assertEquals(
            'INSERT INTO test_table (x, y) VALUES (?, ?)',
            $query->build()
        );
    }
}